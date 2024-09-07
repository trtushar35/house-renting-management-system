<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flat;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function bookingList($userId)
    {
        if (auth()->guard('tenantCheck')->check()) {
            $bookings = Booking::where('tenant_id', $userId)->get();
        } elseif (auth()->guard('ownerCheck')->check()) {
            $bookings = Booking::where('owner_id', $userId)->get();
        }
        return view('frontend.pages.booking.list', compact('bookings'));
    }

    public function bookNow($flatId)
    {
        $flatDetails = Flat::findOrFail($flatId);
        $user = null;
        $tenant = null;
        $owner = null;

        // user check
        if ($user = auth()->guard('tenantCheck')->user()) {
            $tenant = $user->id;
        } elseif ($user = auth()->guard('ownerCheck')->user()) {
            $owner = $user->id;
        }

        // flat owner can't book his posted flat
        if ($owner && $flatDetails->house->house_owner_id == $owner) {
            return redirect()->back()->with('error', 'You cannot book your own flat.');
        }

        // check booking status rejected
        if ($tenant && Booking::where('flat_id', $flatId)->where('tenant_id', $tenant)->where('booking_status', 'Rejected')->exists()) {
            return redirect()->back()->with('error', 'You are rejected by owner.');
        } elseif ($owner && Booking::where('flat_id', $flatId)->where('owner_id', $owner)->where('booking_status', 'Rejected')->exists()) {
            return redirect()->back()->with('error', 'You are rejected by owner.');
        }

        // if tenant cancel booking he can book flat again so status update to pending
        if ($tenant && Booking::where('flat_id', $flatId)->where('tenant_id', $tenant)->where('booking_status', 'Cancel')->exists()) {
            Booking::where('flat_id', $flatId)->where('tenant_id', $tenant)->update([
                'booking_status' => 'Pending',
            ]);
            return redirect()->route('frontend.booking.list', $tenant)->with('success', 'Booking successful.');
        } elseif ($owner && Booking::where('flat_id', $flatId)->where('owner_id', $owner)->where('booking_status', 'Cancel')->exists()) {
            Booking::where('flat_id', $flatId)->where('owner_id', $owner)->update([
                'booking_status' => 'Pending',
            ]);
            return redirect()->route('frontend.booking.list', $owner)->with('success', 'Booking successful.');
        }

        // check booking exists or not
        if ($tenant && Booking::where('flat_id', $flatId)->where('tenant_id', $tenant)->exists()) {
            return redirect()->back()->with('error', 'You have already booked this flat.');
        } elseif ($owner && Booking::where('flat_id', $flatId)->where('owner_id', $owner)->exists()) {
            return redirect()->back()->with('error', 'You have already booked this flat.');
        }

        if ($tenant) {
            Booking::create([
                'flat_id' => $flatDetails->id,
                'tenant_id' => $tenant,
                'rent' => $flatDetails->rent,
                'booking_status' => 'Pending',
            ]);
            return redirect()->route('frontend.booking.list', $tenant)->with('success', 'Booking successful.');
        } elseif ($owner) {
            Booking::create([
                'flat_id' => $flatDetails->id,
                'owner_id' => $owner,
                'rent' => $flatDetails->rent,
                'booking_status' => 'Pending',
            ]);
            return redirect()->route('frontend.booking.list', $owner)->with('success', 'Booking successful.');
        } else {
            return redirect()->back()->with('error', 'Unauthorized.');
        }
    }


    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking) {
            $booking->update([
                'booking_status' => 'cancel',
                'payment_status' => 'cancel_booking',
            ]);
        }
        return redirect()->back()->with('success', 'Booking cancel successfully.');
    }
}

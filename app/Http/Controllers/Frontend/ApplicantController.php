<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Flat;
use App\Models\Payment;

class ApplicantController extends Controller
{
    public function applicantList($id)
    {
        $bookings = Booking::whereHas('flat.house', function ($query) use ($id) {
            $query->where('house_owner_id', $id);
        })->with(['tenant', 'houseOwner', 'flat', 'flat.house', 'payments'])->get();
        return view('frontend.pages.applicant.list', compact('bookings'));
    }

    public function applicantAccept($id)
    {
        $booking = Booking::find($id);
        $booking->update([
            'booking_status' => 'Approved',
        ]);

        $flat = Flat::find($booking->flat_id);
        if (!$flat) {
            return redirect()->back()->with('error', 'Flat not found.');
        }

        $flat->update([
            'availability' => 0,
        ]);
        return redirect()->back()->with('success', 'Booking approved successfully.');
    }

    public function applicantReject($id)
    {
        $booking = Booking::find($id);
        $booking->update([
            'booking_status' => 'Rejected',
        ]);
        return redirect()->back()->with('error', 'Booking rejected successfully.');
    }
}

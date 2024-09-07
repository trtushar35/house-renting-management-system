<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tenant;
use Illuminate\Http\Request;

class HouseOwnerController extends Controller
{
    public function tenantList($houseOwnerId)
    {
        $bookings = Booking::where('booking_status', 'Approved')
            ->whereHas('flat.house', function ($query) use ($houseOwnerId) {
                $query->where('house_owner_id', $houseOwnerId);
            })->with(['flat', 'tenant', 'flat.house.houseOwner'])->get();
        return view('frontend.pages.tenant.list', compact('bookings'));
    }

    public function tenantDetails($id) {
        $tenantDetails = Tenant::find($id);
        $bookings = Booking::where('tenant_id', $id)->with('flat.house')->first();
        return view('frontend.pages.tenant.details', compact('tenantDetails', 'bookings'));
    }
}

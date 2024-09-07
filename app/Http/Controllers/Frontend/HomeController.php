<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use App\Models\Flat;
use App\Models\House;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function __construct()
    {

    }

    public function index()
    {
        $houses = Flat::with('flatImages')->where('availability', 1)->get();
        return view('frontend.pages.home', compact('houses'));
    }

    public function allProperty()
    {
        $houses = Flat::with('flatImages')->get();
        return view('frontend.pages.allProperty', compact('houses'));
    }

    public function dhakaDivision() {
        $housesInDhaka = House::where('division', 'dhaka')->pluck('id');
        $houses = Flat::with('house')->whereIn('house_id', $housesInDhaka)->get();
        return view('frontend.pages.division.dhaka', compact('houses'));
    }

    public function khulnaDivision() {
        $housesInKhulna = House::where('division', 'khulna')->pluck('id');
        $houses = Flat::with('house')->whereIn('house_id', $housesInKhulna)->get();
        return view('frontend.pages.division.khulna', compact('houses'));
    }

    public function mymensinghDivision() {
        $housesInMymensingh = House::where('division', 'mymensingh')->pluck('id');
        $houses = Flat::with('house')->whereIn('house_id', $housesInMymensingh)->get();
        return view('frontend.pages.division.mymensingh', compact('houses'));
    }

    public function searchFlat(Request $request) {
        if ($request->search) {
            $houses = Flat::where('availability', 1)->where('address', 'LIKE', '%' . $request->search . '%')->get();
        } else {
            $houses = House::where('status', '=', 'Available')->get();
        }
        return view('frontend.pages.search', compact('houses'));
    }

}

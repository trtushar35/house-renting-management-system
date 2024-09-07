<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Flat;
use App\Models\FlatImage;
use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HouseController extends Controller
{
    public function houseCreate(){
         return view('frontend.pages.house.create');
    }

    public function houseStore(Request $request) {
        $validate = Validator::make($request->all(), [
            'house_name' => 'required',
            'house_number'=> 'required',
            'address'=> 'required',
            'division'=> 'required',
            'city'=> 'required',
            'country'=> 'required',
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->getMessageBag());
        }

        House::create([
            'house_owner_id' =>  auth('ownerCheck')->user()->id,
            'house_name' => $request->house_name,
            'house_number' => $request->house_number,
            'address' => $request->address,
            'division' => $request->division,
            'city'=> $request->city,
            'country'=> $request->country,
        ]);
        return redirect()->route('frontend.house.list')->with('success','Property added successfully');
    }

    public function houseEdit($id) {
        $house = House::findOrFail($id);
        return view('frontend.pages.house.edit', compact('house'));
    }

    public function houseUpdate(Request $request, $id) {
        $house = House::findOrFail($id);
        $house->update([
            'house_name' => $request->house_name,
            'house_number' => $request->house_number,
            'address' => $request->address,
            'division' => $request->division,
            'city'=> $request->city,
            'country'=> $request->country,
        ]);
        return redirect()->route('frontend.house.list')->with('success','House information updated successfully.');
    }

    public function houseList() {
        $houseList = House::where('house_owner_id', auth('ownerCheck')->user()->id)->get();
        return view('frontend.pages.house.list', compact('houseList'));
    }

    public function houseDetails($flatId) {
        $flatDetails = Flat::find($flatId);
        $getFlatImages = null;
        if ($flatDetails) {
            $getFlatImages = FlatImage::where('flat_id', $flatId)->first();
        }
        return view('frontend.pages.house.singleHouseDetails', compact('flatDetails', 'getFlatImages'));
    }

    public function houseDelete($id) {
        $house = House::findOrFail($id);
        $house->delete();
        return redirect()->back()->with('success','House deleted successfully.');
    }
}

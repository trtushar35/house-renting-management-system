<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Flat;
use App\Models\SavedProperty;
use Illuminate\Http\Request;

class SavedPropertyController extends Controller
{
    public function addToSave($id)
    {
        $flatDetails = Flat::find($id);

        if ($tenant = auth()->guard('tenantCheck')->user()) {
            SavedProperty::create([
                'tenant_id' => $tenant->id,
                'flat_id' => $flatDetails->id,
            ]);
            return redirect()->route('frontend.saved.list', $tenant->id)->with('success', 'Saved the property successfully.');
        }

        if ($owner = auth()->guard('ownerCheck')->user()) {
            SavedProperty::create([
                'owner_id' => $owner->id,
                'flat_id' => $flatDetails->id,
            ]);
            return redirect()->route('frontend.saved.list', $owner->id)->with('success', 'Saved the property successfully.');
        }
    }

    public function savedList($id)
    {
        if (auth()->guard('tenantCheck')->user()) {
            $flatImages = Flat::with('flatImages')->find($id);
            $flatDetails = SavedProperty::with('flat','flat.house.houseOwner')->where('tenant_id', auth('tenantCheck')->user()->id)->get();
            return view('frontend.pages.savedProperty.list', compact('flatDetails', 'flatImages'));
        }
        if(auth()->guard('ownerCheck')->user()) {
            $flatImages = Flat::with('flatImages')->find($id);
            $flatDetails = SavedProperty::with('flat','flat.house.houseOwner')->where('owner_id', auth('ownerCheck')->user()->id)->get();
            return view('frontend.pages.savedProperty.list', compact('flatDetails'));
        }
    }

    public function savedPropertyView($id) {
        $flatDetails = SavedProperty::with('flat.house.houseOwner')->find($id);
        return view('frontend.pages.savedProperty.view', compact('flatDetails'));
    }

    public function savedPropertyDelete($id) {
        $flatDetails = SavedProperty::find($id);
        $flatDetails->delete();
        return redirect()->back()->with('success','Delete successfully.');
    }

}

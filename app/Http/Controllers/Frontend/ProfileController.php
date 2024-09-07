<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\HouseOwner;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profileView()
    {
        return view('frontend.pages.profile.view');
    }

    public function profileEdit($id)
    {
        if (auth()->guard('tenantCheck')->check()) {
            $profile = auth()->guard('tenantCheck')->user();
            $view = 'tenant_profile';
        } elseif (auth()->guard('ownerCheck')->check()) {
            $profile = auth()->guard('ownerCheck')->user();
            $view = 'owner_profile';
        } else {
            return redirect()->back();
        }
        return view('frontend.pages.profile.edit', compact('profile', 'view'));
    }

    public function profileUpdate(Request $request, $id)
    {
        // dd($request->all());
        if (auth()->guard('tenantCheck')->check()) {
            $profile = Tenant::find($id);
            $fileName = $profile->image;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = date('Ymdhis') . '.' . $file->getClientOriginalExtension();

                $file->storeAs('/tenants', $fileName);
            }
        } elseif (auth()->guard('ownerCheck')->check()) {
            $profile = HouseOwner::find($id);
            $fileName = $profile->image;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = date('Ymdhis') . '.' . $file->getClientOriginalExtension();

                $file->storeAs('/owners', $fileName);
            }
        } else {
            return redirect()->back()->with('error', 'Unathorised user');
        }

        $profile->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'image' => $fileName,
        ]);
        return redirect()->route('frontend.profile.view')->with('success', 'Profile updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Flat;
use App\Models\FlatImage;
use App\Models\House;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FlatController extends Controller
{
    public function flatCreate($houseId)
    {
        $house = House::findOrFail($houseId);
        return view('frontend.pages.flat.create', compact('house'));
    }

    public function flatStore(Request $request)
    {
        // dd($request->all());
        $validate = Validator::make($request->all(), [
            'address' => 'required',
            'floor_number' => 'required|numeric',
            'flat_number' => 'required',
            'num_bedrooms' => 'required|numeric',
            'num_bathrooms' => 'required|numeric',
            'rent' => 'required|numeric',
            'availability' => 'required',
            'available_date' => 'required',
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->getMessageBag());
        }

        Flat::create([
            'house_id' => request()->house_id,
            'address' => $request->address,
            'floor_number' => $request->floor_number,
            'flat_number' => $request->flat_number,
            'num_bedrooms' => $request->num_bedrooms,
            'num_bathrooms' => $request->num_bathrooms,
            'rent' => $request->rent,
            'availability' => $request->availability,
            'available_date' => $request->available_date,
        ]);

        return redirect()->back()->with('success', 'Flat create successfully.');
    }

    public function flatEdit($flatId)
    {
        $flat = Flat::findOrFail($flatId);
        return view('frontend.pages.flat.edit', compact('flat'));
    }

    public function flatUpdate(Request $request, $flatId)
    {
        $flat = Flat::findOrFail($flatId);
        $flat->update([
            'address' => $request->address,
            'floor_number' => $request->floor_number,
            'flat_number' => $request->flat_number,
            'num_bedrooms' => $request->num_bedrooms,
            'num_bathrooms' => $request->num_bathrooms,
            'rent' => $request->rent,
            'availability' => $request->availability,
            'available_date' => $request->available_date,
        ]);
        return redirect()->route('frontend.single.flat.details', $flatId)->with('success', 'Flat details updated successfully.');
    }

    public function flatDelete($flatId)
    {
        $flat = Flat::findOrFail($flatId);
        $flat->delete();
        return redirect()->back()->with('success', 'Flat deleted successfully.');
    }

    public function flatList($houseId)
    {
        $flatDetails = Flat::with('house')->where('house_id', $houseId)->get();
        return view('frontend.pages.flat.list', compact('flatDetails'));
    }

    public function singleFlat($flatId)
    {
        $flatDetails = Flat::find($flatId);

        $getFlatImages = null;

        if ($flatDetails) {
            $getFlatImages = FlatImage::where('flat_id', $flatId)->first();
        }
        return view('frontend.pages.flat.singleFlatDetails', compact('flatDetails', 'getFlatImages'));
    }

    public function singleFlatImageUpload(Request $request, $id)
    {
        $flat = Flat::find($id);

        $validator = Validator::make($request->all(), [
            'image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $existingImages = FlatImage::where('flat_id', $flat->id)->get();

        if ($request->hasFile('image') && count($existingImages) > 0) {
            foreach ($existingImages as $existingImage) {
                Storage::delete('public/flatImage/' . $existingImage->square_footage);
            }
            FlatImage::where('flat_id', $flat->id)->delete();
        }


        $imagePaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {

                $fileName = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/flatImage', $fileName);
                $imagePaths[] = $fileName;
            }
        }

        foreach ($imagePaths as $path) {
            FlatImage::create([
                'flat_id' => $flat->id,
                'square_footage' => $path,
            ]);
        }

        return redirect()->back()->with("success", "Flat images uploaded successfully.");
    }

    public function singleFlatImageDelete($imageId) {
        $image = FlatImage::find($imageId);
        $image->delete();
        return redirect()->back()->with('success','Image deleted successfully.');
    }
}

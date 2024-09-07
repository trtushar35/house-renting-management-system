<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\HouseOwner;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function registration()
    {
        return view('frontend.pages.registration');
    }

    public function registrationPost(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'password' => 'required',
            'role' => 'required',
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->getMessageBag());
        }

        if ($request->role == 'house_owner') {
            HouseOwner::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ]);
        } else {
            Tenant::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ]);
        }
        return redirect()->route('frontend.user.login')->with('success', 'Registration successful.');
    }

    public function loginPage()
    {
        return view('frontend.pages.login');
    }

    public function logInPost(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate->getMessageBag());
        }

        $credentials = $request->except('_token');
        if (auth()->guard('tenantCheck')->attempt($credentials) || auth()->guard('ownerCheck')->attempt($credentials)) {
            return redirect()->route('frontend.home');
        } else {
            return redirect()->back()->withErrors(['email' => 'Invalid credentials.']);
        }
    }

    public function logOut(Request $request)
    {
        if (auth('tenantCheck')->check()) {
            auth('tenantCheck')->logout();
        } elseif (auth('ownerCheck')->check()) {

            auth('ownerCheck')->logout();
        }
        return redirect()->route('frontend.user.login')->with('success', 'You have been logged out.');
    }
}

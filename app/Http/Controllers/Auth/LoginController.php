<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        $credentials = $request->except(['_token']);

        User::query()->where('email', $request->email)->where('status', '=', 1)->firstOrFail();

        if (auth()->attempt($credentials)) {
            return redirect("/");
        }

        session()->flash('message', 'Invalid credentials');

        return redirect()->back();
    }


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'phone_number' => 'required',
        ]);

        // $user =  new User();
        // $user->name = $request->input('name');
        // $user->email = $request->input('email');
        // $user->password = $request->input('password');
        // $user->phone_number = $request->input('phone_number');

        // dd($user->save());

        try {
            User::create([
                'name' => $request->input('name'),
                'email' => strtolower($request->input('email')),
                'password' => Hash::make($request->input('password')),
                'phone_number' => trim($request->input('phone_number')),
            ]);

            session()->flash('message', 'Your account is created');
        } catch (\Illuminate\Database\QueryException $e) {
            session()->flash('message', 'Your registration is getting problem');
        }

        return redirect(route('home'));
    }

    public function destroy(Request $request){
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(route('home'));
    }
}

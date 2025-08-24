<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register()
    {
        
        return view('auth.register');
    }

    public function create(RegisterRequest $request)
    {
        // usersテーブルに保存
        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        Auth::login($user);

        // プロフィール設定画面へリダイレクト
        return redirect()->route('profile.edit')->with('just_registered', true);
    }

    
     
    }

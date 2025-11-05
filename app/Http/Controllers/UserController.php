<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    //
    public function __construct() {}

    public function register(Request $request) {
        $name = $request -> name;
        $email = $request -> email;
        $password = $request -> password;

        if (!$email || !$password) {
            return response() -> json([
                'detail' => 'Email or password are required.'
            ], 400);
        }

        if (User::where('email', $email) -> exists()) {
            return response() -> json([
                'detail ' => "Email already exists."
            ], 400);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        return $this -> response($user, 'User registered successfully.', 201);
    }
}

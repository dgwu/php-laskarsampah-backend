<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use Hash;

class UserController extends Controller
{
    public function login(Request $request) {
        $errorCode = 403;
        $result = null;
        $errorMessage = '';

        $user = User::where('telepon', $request->phone)
            ->where('password', $request->password)
            ->first();

        if (!empty($user)) {
            $errorCode = 200;
            $result['api_token'] = $user->api_token;
        } else {
            $errorMessage = 'Wrong credentials.';
        }


        return $this->reply($result, $errorCode, $errorMessage);
    }
}

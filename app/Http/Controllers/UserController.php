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

    public function getProfile(Request $request) {
        $errorCode = 403;
        $result = null;
        $errorMessage = '';

        if (!empty($request->api_token)) {
            $isUserExists = User::select('id', 'nama', 'telepon', 'alamat')
                ->where('api_token', $request->api_token)
                ->first();

            if (!empty($isUserExists)) {
                $errorCode = 200;
                $result['user'] = $isUserExists;
            } else {
                $errorMessage = "Unauthorized access.";
            }
        } else {
            $errorMessage = "Unauthorized access.";
        }

        return $this->reply($result, $errorCode, $errorMessage);
    }
}

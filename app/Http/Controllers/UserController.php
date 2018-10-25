<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use Hash;
use Validator;

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
            $generatedToken = str_random(20);
            $user->api_token = $generatedToken;
            $user->save();
            $result['api_token'] = $generatedToken;
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

    public function register(Request $request) {
        // nama, email, telepon, password
        $errorCode = 403;
        $result = null;
        $errorMessage = '';

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|min:3',
            'email'=> 'nullable|email|unique:m_users,email',
            'telepon' => 'required|min:5',
            'password' => 'required|string|min:3'
        ]);

        if (!$validator->fails()) {
            try {
                $newUser = new User();
                $newUser->nama = $request->nama;
                $newUser->email = $request->email;
                $newUser->telepon = $request->telepon;
                $newUser->password = $request->password;

                $generatedToken = str_random(20);
                $newUser->api_token = $generatedToken;

                $newUser->save();
                $result['api_token'] = $generatedToken;
                $errorCode = 200;
            } catch (\Exception $e) {
                $errorCode = 500;
                $errorMessage = $e->getMessage();
            }
        } else {
            $errorMessage = $validator->errors()->first();
        }

        return $this->reply($result, $errorCode, $errorMessage);
    }
}

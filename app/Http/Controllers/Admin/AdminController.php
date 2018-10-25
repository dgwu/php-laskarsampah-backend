<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use \App\Http\Models\Admin\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Log;

class AdminController extends Controller
{

    public function login(Request $request) {
        $errorCode = 403;
        $result = null;
        $errorMessage = '';

        Log::info("phone == ".$request->phone);
        
        $user = $this->checkUser($request->phone);

        if(empty($user)) {
            $errorMessage = 'Wrong credentials.';
            return $this->reply($result, $errorCode, $errorMessage);
        }

        if (!$this->isValidPassword($request->password, $user->password)) {
            Log::info("password not matches");
            $errorMessage = 'Password not matches';
            return $this->reply($result, $errorCode, $errorMessage);
        }

        $token = Str::random();
        if($this->updateUserToken($user->id, $token) != 1) {
            Log::info("Error system");
            return $this->reply($result, $errorCode, $errorMessage);
        }

        $errorCode = 200;
        $result['api_token'] = $token;
        
        return $this->reply($result, $errorCode, $errorMessage);
    }

    private function checkUser($phone) {
        return User::where('telepon', $phone)->first();
    }

    private function isValidPassword($inputPassword, $password) {
        return Hash::check($inputPassword, $password);
    }

    private function updateUserToken($id, $token) {
        return User::where('id', $id)->update(array('api_token' => $token));
    }

}

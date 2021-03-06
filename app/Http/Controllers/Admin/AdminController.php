<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\User;
use Log;
use Validator;

use \App\Http\Models\Admin\Admin;

class AdminController extends Controller
{

    public function login(Request $request) {
        $errorCode = 403;
        $errorMessage = '';
        $result = null;

        Log::info("phone == ".$request->phone);
        
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password'=> 'required|string'
        ]);

        if ($validator->fails()) {
            $errorMessage = 'Parameter tidak valid';
            return $this->reply($result, $errorCode, $errorMessage);
        }

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

    public function checkCustomerQR(Request $request) {
        $errorCode = 403;
        $result = null;
        $errorMessage = '';

        if (!empty($request->telepon)) {
            $isUserExists = User::select('id', 'nama', 'telepon', 'alamat')
                ->where('telepon', $request->telepon)
                ->first();

            if (!empty($isUserExists)) {
                $errorCode = 200;
                $result['customer'] = $isUserExists;
            } else {
                $errorCode = 404;
                $errorMessage = "User not Found";
            }
        } else {
            $errorMessage = "Unauthorized access";
        }

        return $this->reply($result, $errorCode, $errorMessage);
    }

    public function getProfile(Request $request) {
        $errorCode = 403;
        $result = null;
        $errorMessage = '';

        if (!empty($request->api_token)) {
            $isUserExists = Admin::select('id', 'nama', 'telepon', 'alamat', 'longitude','latitude', 'api_token')
                ->where('api_token', $request->api_token)
                ->first();

            if (!empty($isUserExists)) {
                $errorCode = 200;
                $result['admin'] = $isUserExists;
            } else {
                $errorCode = 404;
                $errorMessage = "Admin not Found";
            }
        } else {
            $errorMessage = "Unauthorized access";
        }

        return $this->reply($result, $errorCode, $errorMessage);
    }

    private function checkUser($phone) {
        return Admin::where('telepon', $phone)->first();
    }

    private function isValidPassword($inputPassword, $password) {
        return Hash::check($inputPassword, $password);
    }

    private function updateUserToken($id, $token) {
        return Admin::where('id', $id)->update(array('api_token' => $token));
    }

}

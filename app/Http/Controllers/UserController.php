<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use \App\WasteBank;
use \App\News;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
{
    public function login(Request $request) {
        $errorCode = 403;
        $result = null;
        $errorMessage = '';

        $user = User::where('telepon', $request->phone)
            ->first();

        if (!empty($user)) {
            if (Hash::check($request->password, $user->password)) {
                $errorCode = 200;
                $generatedToken = str_random(20);
                $user->api_token = $generatedToken;
                $user->save();
                $result['api_token'] = $generatedToken;
            } else {
                $errorMessage = 'Wrong credentials.';
            }
            
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


    //MARK : Okky = Buat Dapetin list Bank sampah  By ID
    public function getWasteBankBy(Request $request) {
        $errorCode = 403;
        $result = null;
        $errorMessage = '';

        if (!empty($request->id)) {
            $bankS = WasteBank::select('id', 'namaBank', 'alamat', 'longtitude', 'latitude', 'namaAdmin','telepon','status')
                ->where('id', $request->id)
                ->first();

            if (!empty($bankS)) {
                $errorCode = 200;
                $result['WasteBank'] = $bankS;
            } else {
                $errorMessage = "Unauthorized access.";
            }
        } else {
            $errorMessage = "Unauthorized access.";
        }

        return $this->reply($result, $errorCode, $errorMessage);
    }

    //MARK : Okky = Buat Dapetin list Bank sampah
    public function getWasteBank() {
        $errorCode = 403;
        $result = null;
        $errorMessage = '';

        $banklist = WasteBank:: select('id', 'namaBank', 'alamat', 'longtitude', 'latitude', 'namaAdmin','telepon','status')
                ->get();
    
        if ($banklist->isNotEmpty()) {
            $errorCode = 200;
            $result['bank'] = $banklist;
        } else {
            $errorMessage = 'Empty.';
        }

        return $this->reply($result, $errorCode, $errorMessage);
    }

    //MARK : Okky = Buat Dapetin all list News
    public function getNews() {
        $errorCode = 403;
        $result = null;
        $errorMessage = '';

        $newslist = News:: select('id', 'judul', 'tanggal', 'content', 'url', 'like','status','createBy')
                ->get();
    
        if ($newslist->isNotEmpty()) {
            $errorCode = 200;
            $result['news'] = $newslist;
        } else {
            $errorMessage = 'Empty.';
        }

        return $this->reply($result, $errorCode, $errorMessage);
    }


     //MARK : Okky = Buat Dapetin all list News
    public function getNewsBy(Request $request) {
        $errorCode = 403;
        $result = null;
        $errorMessage = '';
        
        if (!empty($request->id)) {
            $News = News::select('id', 'judul', 'tanggal', 'content', 'url', 'like','status','createBy')
                ->where('id', $request->id)
                ->first();

            if (!empty($News)) {
                $errorCode = 200;
                $result['news'] = $News;
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
            'telepon' => 'required|min:5|unique:m_users,telepon',
            'password' => 'required|string|min:3'
        ]);

        if (!$validator->fails()) {
            try {
                $newUser = new User();
                $newUser->nama = $request->nama;
                $newUser->email = $request->email;
                $newUser->telepon = $request->telepon;
                $newUser->password = Hash::make($request->password);

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

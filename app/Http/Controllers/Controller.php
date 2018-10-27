<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function reply($result = null, $errorCode = 200, $errorMessage = '') {
        $response = [
            'errorCode' => $errorCode,
            'errorMessage' => $errorMessage,
            'result' => $result,
        ];
        Log::info(json_encode([
            'endpoint' => $this->request->url(),
            'request' => $this->request->all(),
            'response' => $response
        ]));

        return response()->json($response, $errorCode, [], JSON_NUMERIC_CHECK);
    }
}

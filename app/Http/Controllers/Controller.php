<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function reply($result = null, $errorCode = 200, $errorMessage = '') {

        return response()->json([
            'errorCode' => $errorCode,
            'errorMessage' => $errorMessage,
            'result' => $result,
        ], $errorCode, [], JSON_NUMERIC_CHECK);
    }
}

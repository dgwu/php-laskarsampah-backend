<?php

namespace App\Http\Controllers;
use DB;

class WasteController extends Controller
{
    public function pricelist() {
        $errorCode = 403;
        $result = null;
        $errorMessage = '';

        $pricelist = DB::table('m_priceListItem')
            ->select('id', 'item_name', 'item_unit', 'item_poin', 'item_price')
            ->get();

        if ($pricelist->isNotEmpty()) {
            $errorCode = 200;
            $result['price_list'] = $pricelist;
        } else {
            $errorMessage = 'Empty.';
        }

        return $this->reply($result, $errorCode, $errorMessage);
    }
}

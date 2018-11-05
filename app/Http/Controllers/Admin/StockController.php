<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Log;
use Exception;
use Validator;

use \App\Http\Models\Admin\Admin;
use \App\Http\Models\Transaction;
use \App\Http\Models\TransactionDetail;
use \App\Http\Models\PriceListItem;


class StockController extends Controller
{

    public function inputStock(Request $request) {
        $errorCode = 403;
        $result = null;
        $errorMessage = 'Parameter tidak valid';

        // Log::info($request->items);
        $validator = Validator::make($request->all(), [
            'userId' => 'required',
            'userAdmin'=> 'required',
            'totalPoint'=> 'required',
            'items'=> 'required',
        ]);

        if ($validator->fails()) {
            return $this->reply($result, $errorCode, $errorMessage);
        }

        $transaction = $this->getDataTransaction($request);

        try {
            $resultSave = $transaction->save();
            if($resultSave != 1) {
                $errorMessage = "Cannot save transaction";
                return $this->reply($result, $errorCode, $errorMessage);
            }

            Log::info("ID Transaksi = ".$transaction->idTransaksi);
            
            $this->saveDetail($request->items, $transaction->idTransaksi);
            // $this->saveDetail($request->items, 1);
            $errorCode = 200;
            $errorMessage = '';
            return $this->reply($result, $errorCode, $errorMessage);
            
        }catch (\Exception $e) {
            Log::info("error save");
            return $e->getMessage();
        }
    }

    private function getDataTransaction($request) {
        $transaction = new Transaction();
        $transaction->tanggal = date('Y-m-d H:i:s');
        $transaction->idUser = $request->userId;
        $transaction->idAdmin = $request->userAdmin;
        $transaction->grandTotal = $request->totalPoint;
        $transaction->status = "Aktif";
        return $transaction;
    }

    private function saveDetail($items, $transId) {
        Log::info(" data = ".$items);
        $dataItems = json_decode($items, true);
        foreach($dataItems as $item) {
            // Log::info("item name = ".$item['itemName']);
            $this->saveDataDetail($item, $transId);
        }
    }

    private function saveDataDetail($item, $transId) {
        Log::info("data === ".$item['itemName']. " == ".$item['weight']. " ".$item['point']);
        $transDetail = new TransactionDetail();
        $transDetail->trans_id = $transId;
        $transDetail->item_id = $transId;
        $transDetail->nama_item = $item['itemName'];
        $transDetail->jumlah = $item['weight'];
        $transDetail->poin = $item['point'];
        $totalPoint = $item['weight'] * $item['point'];
        $transDetail->totalPoin = $totalPoint;
        Log::info("totalPoint === ".$totalPoint);
        $transDetail->save();
    }

    public function getAdminHistory(Request $request) {

        $errorCode = 404;
        $result = null;
        $errorMessage = '';
    
        $validator = Validator::make($request->all(), [
            'api_token' => 'required|string|min:3',
            'start_date'=> 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d'
        ]);
    
        if (!$validator->fails()) {
            $isUserExists = Admin::with(['transactions.detail', 'transactions.user'])
                ->select('id')
                ->where('api_token', $request->api_token)
                ->first();
    
            if (empty($isUserExists)) {
                $errorCode = 404;
                $errorMessage = "Admin Not Found";
                return $this->reply($result, $errorCode, $errorMessage);
            }
    
//            $listTrx =Transaction::
//            leftJoin('m_users', 'm_users.id', '=', 'transaksi_h.idUser')
//                ->select('transaksi_h.idTransaksi', 'transaksi_h.tanggal', 'm_users.nama', 'm_users.email', 'm_users.telepon', 'transaksi_h.grandTotal', 'transaksi_h.status')
//                ->where('transaksi_h.idAdmin', $isUserExists["id"])
//                ->whereBetween('tanggal', [$request->start_date, $request->end_date])
//                ->get();

            $listTrx = $isUserExists->transactions;
//                ->whereBetween('tanggal', [$request->start_date, $request->end_date]);
//                ->get();
    
    
            $errorCode = 200;
            $result['transactions'] = $listTrx;
            $errorMessage = 'SUCCESS';
        }
    
        return $this->reply($result, $errorCode, $errorMessage);
    }
        
    public function getTransactionDetail(Request $request) {
        $errorCode = 403;
        $result = null;
        $errorMessage = '';

        if (!empty($request->trx_id)) {
            $listTrx =TransactionsDetail::
            leftJoin('transaksi_h', 'transaksi_h.idTransaksi', '=', 'transaksi_d.trans_id')
                ->select('transaksi_d.*')
                ->where('transaksi_d.trans_id',$request->trx_id)
                ->get();

            if (!empty($listTrx)) {
                $errorCode = 200;
                $result = $listTrx;
            } else {
                $errorCode = 404;
                $errorMessage = "Trx not Found";
            }
        } else {
            $errorMessage = "Unauthorized access";
        }

        return $this->reply($result, $errorCode, $errorMessage);
    }

    public function getItem() {
        return json_encode(PriceListItem::select('id', 'item_name', 'item_poin', 'item_price')->get(), JSON_NUMERIC_CHECK);
    }
}

<?php

namespace App\Http\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Transaction extends Model
{
    protected $table = "transaksi_h";
    
    protected $primaryKey = "idTransaksi";

    public function user() {
        return $this->belongsTo('App\User', 'idUser');
    }

    public function admin() {
        return $this->belongsTo('App\Http\Models\Admin\Admin', 'idAdmin');
    }

    public function detail() {
        return $this->hasMany('App\Http\Models\TransactionDetail', 'trans_id');
    }
}

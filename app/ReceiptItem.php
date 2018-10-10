<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ReceiptItem extends Model
{

    protected $fillable = array('sale_number', 'item_number', 'amount','receipt_id');
   
    public function receipt(){
         return $this->hasOne('App\Receipt');
    }


}
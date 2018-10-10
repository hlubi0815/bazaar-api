<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Receipt extends Model
{

    protected $fillable = array('settled','bazaar_id');
   
    public function bazaar(){
         return $this->hasOne('App\Bazaar');
    }

    public function receipt_items(){
        return $this->hasMany('App\ReceiptItem');
    }


}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class SaleNumber extends Model
{
    protected $table = 'sale_numbers';

    protected $fillable = array('sale_number','user_id','bazaar_id');

    public function bazaar(){
        return $this->hasOne('App\Bazaar');
    }

    public function user(){
        return $this->hasOne('App\User');
    }


}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Bazaar extends Model 
{

    protected $table = 'bazaar';

    protected $fillable = array('id','name', 'bazaardate', 'listnumber_start',
                            'listnumber_end','fee','percentageoff','change');


    protected $casts = [
        'bazaardate' => 'datetime:Y-m-d',
    ];


    public function users(){
         return $this->belongsToMany('App\User','users_bazaars')->withPivot(['user_id', 'bazaar_id', 'sale_number'])->withTimestamps();
    }

    public function saleNumbers(){
        return $this->hasMany('App\SaleNumber');
    }

    public function items(){
        return $this->belongsToMany('App\Item','bazaar_items','bazaar_id','item_id')->withTimestamps();
   }

   public function receipts(){
        return $this->hasMany('App\Receipt');
   }
}
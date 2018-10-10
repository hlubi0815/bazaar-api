<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Item extends Model 
{

    protected $fillable = array( 'item_number', 'description', 'size', 'price');
   
    public function bazaar(){
         return $this->belongsToMany('App\Bazaar','bazaar_items','item_id','bazaar_id')->withTimestamps();
    }
    
    public function user(){
        return $this->belongsTo('App\User');
    }

}
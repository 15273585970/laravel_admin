<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    //
    public function category()
    {
        //return $this->hasOne(GoodsCategory::class);
        return $this->hasOne('App\Models\GoodsCategory','id','category_id');
    }
}

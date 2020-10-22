<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsCategory extends Model
{
    //
    public function Goods()
    {
       // return $this->belongsTo(Goods::class);
        return $this->belongsTo('App\Models\Goods','category_id','id');
    }
}

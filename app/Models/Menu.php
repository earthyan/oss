<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/28
 * Time: 10:27
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    protected $table = 'oss_menu';


    public function product(){
        $this->belongsTo(Product::class,'product_id');
    }



}
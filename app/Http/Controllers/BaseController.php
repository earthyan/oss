<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/3
 * Time: 16:08
 */

namespace App\Http\Controllers;


use App\Models\Product;

class BaseController extends Controller
{
    public function __construct()
    {
        $path = request()->path();
        $product = Product::where('key',$path)->first();
        if(empty($product)){
            return response()->json(['msg'=>'该游戏不存在','code'=>400]);
        }
        session(['product_key'=>$path]);
        session(['product_id'=>$product['id']]);
    }

}
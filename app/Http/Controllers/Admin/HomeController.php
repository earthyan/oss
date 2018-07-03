<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/25
 * Time: 14:50
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class HomeController extends BaseController
{
    public function __construct()
    {

    }

    /**
     * 产品列表页面
     * 每个产品对应一个key 会体现在url上
     *
     */
    public function index(){
        $products = Product::all()->toArray();
        if(empty($products)){
            return response()->json(['msg'=>'暂无游戏产品','code'=>200]);
        }
        $result = [];
        foreach ($products as $product){
            $result[$product['category']][] = $product;
        }

        return response()->json(['data'=>$result,'code'=>200]);
    }



}
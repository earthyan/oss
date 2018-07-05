<?php
/**
 * Created by PhpStorm.
 * User: hainan
 * 创建全局公共函数文件
 * Date: 2018/7/5
 * Time: 9:44
 */

function test(){
    echo 1;
}


function get_menu($product_id){


}


function getAllMenu(){
    $menus = \App\Models\Menu::where('type',1)->get();
    foreach ($menus as $menu){
        $menu->product_name = $menu->product()->name;
    }
    return response()->json($menu);
}
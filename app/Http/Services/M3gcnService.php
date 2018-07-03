<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/27
 * Time: 11:00
 */

namespace App\Http\Services;


use App\Models\tab_m3ghk_result\m3ghk_daily_reg;

class M3gcnService
{

    public function __construct()
    {


    }


    public function test(){
        $result = m3ghk_daily_reg::all();

        return $result;


    }

}
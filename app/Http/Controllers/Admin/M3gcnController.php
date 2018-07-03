<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/27
 * Time: 10:36
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Http\Services\M3gcnService;

class M3gcnController extends BaseController
{
    public function __construct(M3gcnService $service)
    {
        $this->service = $service;

    }

    public function test(Request $request){
        //筛选条件






    }

    public function index(){

    }







}
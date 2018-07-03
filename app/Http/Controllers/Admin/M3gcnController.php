<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/27
 * Time: 10:36
 */

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Http\Services\M3gcnService;

class M3gcnController extends BaseController
{

    private $vzoneid;
    private $startDate;
    private $endDate;
    public function __construct(M3gcnService $service)
    {
        $this->service = $service;
        //默认通配筛选框
        $this->vzoneid = request()->input('vzoneid');//大区
        $this->startDate = request()->input('startDate');
        $this->endDate = request()->input('endDate');

    }

    public function index(){
        dd(1);
        //当前页面
        $menu_id = request()->input('menu_id');
        $menu = Menu::find($menu_id);
        //当前页面所有的模块
        $modules = Menu::where(['type'=>2,'parent_id'=>$menu_id])->get();

        $sql = 'SELECT date(date) as date,sum(`costMoney`) as costMoney
                  FROM m3ghk_daily_item
                 WHERE [date=start,date=end] 
group by date(date)';

        $result = m3ghk_daily_login::query($sql);







    }







}
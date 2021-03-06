<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/27
 * Time: 10:36
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Services\M3ghkService;
use App\Models\Menu;
use App\Models\SqlConfig;
use App\Models\SqlWhere;
use Illuminate\Support\Facades\DB;

class M3ghkController extends BaseController
{

    private $vzoneid;
    private $startDate;
    private $endDate;
    private $connection = "";//需要连接的数据库

    public function __construct(M3ghkService $service)
    {
        parent::__construct();
        $this->service = $service;
        //默认通配筛选框
        $this->vzoneid = request()->has('vzoneid')? request()->input('vzoneid') : '';//大区
        $this->startDate = request()->has('startDate')? request()->input('startDate') : '';
        $this->endDate = request()->has('endDate')? request()->input('endDate') : '';

    }

    public function index(){

        $product_id = session('product_id');//当前产品
        $menu_id = request()->input('menu_id');
        $menu = Menu::find($menu_id);
        //所有模块共有筛选


        //当前页面所有的模块 status=1 已发布
        $modules = Menu::where(['type'=>2,'parent_id'=>$menu_id,'status'=>1])->get();
        if(!empty($modules)){
            $res = [];
            foreach ($modules as $module){
                $sqlConfig = SqlConfig::where('menu_id',$module['menu_id'])->first();
                $sql = $sqlConfig['sql'];
                $head = $sqlConfig['head'];
                //$sql = sprintf($sql,$args);  这里传参
                $res = DB::connection($this->connection)->select($sql);//选择不同的数据库来操作
                $res[] = array(
                    'head'=>$head,
                    'res'=>$res,
                    'menu_id'=>$module['menu_id'],
                    'module_name'=>$module['module_name'],
                    'page_name'=>$module['page_name'],
                );
            }

            //筛选条件
            $sqlWhere = SqlWhere::where('product',$product_id)->get();
            $data = array(
                'res'=>$res,
                'sqlWhere'=>$sqlWhere
            );

            return response()->json(['data'=>$data,'code'=>200]);
        }else{
            return response()->json(['msg'=>'暂无模块，请到配置页面配置相应模块','code'=>400]);
        }
    }
}
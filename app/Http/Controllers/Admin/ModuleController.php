<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/28
 * Time: 10:25
 */

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Models\Menu;
use App\Models\ModuleVersion;
use App\Models\Product;
use App\Models\SqlConfig;
use App\Models\SqlWhere;
use Illuminate\Http\Request;

class ModuleController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $menu = Menu::where(['type'=>1,'product_id'=>session('product_id')])->get();
        if(empty($menu)){
            return response()->json(['msg'=>'暂无数据模块','code'=>400]);
        }
        foreach ($menu as $val){
            $product = Product::find($val['id']);
            $val->productName = $product['name'];
            $val->state = '未发布';
            isset($val->status) && $val->status && $val->state = '已发布';
            isset($val->product_id) && $val->product_id==0 && $val->productName = '所有游戏';
        }
        return response()->json(['data'=>$menu,'msg'=>'success','code'=>200]);
    }

    /***
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPageAction(Request $request){
        try{
            $menu = new Menu();
            $menu->key = session('product_key');
            $menu->product_id = session('product_id');
            $menu->parent_id = $request->get('parent_id');
            $menu->page_name = $request->get('page_name');
            $menu->module_name = '';
            $menu->type = 1;
            $menu->save();
            return response()->json(['msg'=>'success','code'=>200]);
        }catch (\Exception $e){
            return response()->json(['msg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }


    /***
     * 模块配置器 视图
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOrUpdateModuleView(Request $request){
        try{
            $data = [];
            if($request->has('menu_id')){
                $menuId  = $request->input('menu_id');
                $menu = Menu::find($menuId);
                $method = $menu->method;
                $sql = SqlConfig::where('method',$method)->first();
                $version = ModuleVersion::where('menu_id',$menuId);
                $data = array(
                    'menu'=>$menu,
                    'sql'=>$sql,
                    'version'=>$version,
                );
            }
            return response()->json(['data'=>$data,'code'=>200,'msg'=>'success']);
        }catch (\Exception $e){
            return response()->json(['msg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }


    /**
     * 模块配置器  操作
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOrupdateModuleAction(Request $request){
        try{
            if($request->has('menu_id')){
                $menu = Menu::find($request->input('menu_id'));
            }else{
                $menu = new Menu();
            }
            $menu->product_id = session('product_id');
            $menu->parent_id = $request->input('parent_id');
            $menu->page_name = $request->input('page_name');
            $menu->module_name = $request->input('module_name');
            $menu->status = 0;
            $menu->key = session('product_key');
            $menu->type = 2; //2 表示模块
            $menu->save();

            //参数配置
            if($request->has('config_id')){
                $sqlConfig = SqlConfig::find($request->input('config_id'));
            }else{
                $sqlConfig = new SqlConfig();
            }
            $sqlConfig->menu_id = $menu->id;
            $sqlConfig->sql = $request->input('sql');
            $sqlConfig->head = $request->input('head');//json 对象
            $sqlConfig->save();

            //历史版本
            $lastest = ModuleVersion::where('menu_id',$request->input('menu_id'))->latest()->first();
            $version = new ModuleVersion();
            $version->operation = $request->user()->name;//操作人员
            $version->version = !empty($lastest)? $lastest['version']+1: 0;
            $version->menu_id = $request->input('menu_id');
            $version->action_time = time();
            $version->save();
            return response()->json(['msg'=>'success','code'=>200]);
        }catch (\Exception $e){
            return response()->json(['msg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 添加sql 条件参数配置
     */
    public function addOrUpdateSqlWhere(Request $request){
        try{
            if($request->has('id')){
                $sqlWhere = SqlWhere::find($request->input('id'));
            }else{
                $sqlWhere = new SqlWhere();
            }
            $sqlWhere->key = $request->input('key');
            $sqlWhere->name =  $request->input('name');
            $sqlWhere->show_type = $request->input('show_type');
            $sqlWhere->return_type = $request->input('return_type');
            $sqlWhere->content = $request->input('content');
            $sqlWhere->explain = $request->input('explain');
            $sqlWhere->product = session('product_id');
            $sqlWhere->save();
            return response()->json(['msg'=>'success','code'=>200]);
        }catch (\Exception $e){
            return response()->json(['msg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }


    public function delSqlWhere(Request $request){
        try{
            $sqlWhere = SqlWhere::find($request->input('id'));
            if(empty($sqlWhere)){
                return response()->json(['msg'=>'invalid params','code'=>400]);
            }
            $sqlWhere->delete();
            return response()->json(['msg'=>'success','code'=>200]);
        }catch (\Exception $e){
            return response()->json(['msg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }


    /****
     * 发布操作
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function publish(Request $request){
        try{
            $menuId = $request->input('menu_id');
            $menu = Menu::find($menuId);
            if(empty($menu)){
                return response()->json(['msg'=>'invalid params','code'=>400]);
            }
            $menu->status = 1;
            $menu->save();
            return response()->json(['msg'=>'success','code'=>200]);
        }catch (\Exception $e){
            return response()->json(['msg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }


    /****
     * 可考虑软删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request){
        try{
            $menuId = $request->input('menu_id');
            $menu = Menu::find($menuId);
            if(empty($menu)){
                return response()->json(['msg'=>'invalid params','code'=>400]);
            }
            $menu->delete();
            return response()->json(['msg'=>'success','code'=>200]);
        }catch (\Exception $e){
            return response()->json(['msg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }


    /****
     * 复制操作
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function copy(Request $request){
        try{
            $menuId = $request->input('menu_id');//当前的模块
            $menu = Menu::find($menuId);
            if(empty($menu)){
                return response()->json(['msg'=>'invalid params','code'=>400]);
            }
            $new_menu = new Menu();
            $new_menu->product_id = session('product_id');
            $new_menu->parent_id = $request->input('parent_id');
            $new_menu->page_name = $menu->page_name;
            $new_menu->module_name = $menu->module_name;
            $new_menu->status = 0;
            $new_menu->key = session('product_key');
            $new_menu->type = 2; //2 表示模块
            $new_menu->save();
            return response()->json(['msg'=>'success','code'=>200]);
        }catch (\Exception $e){
            return response()->json(['msg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }


    /****
     * 恢复历史版本模块
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recover(Request $request){
        try{
            $versionId = $request->input('version_id');
            $version = ModuleVersion::find($versionId);
            $version->operation = $request->user()->name;
            $version->action_time = time();
            $version->save();
            return response()->json(['msg'=>'success','code'=>200]);
        }catch (\Exception $e){
            return response()->json(['msg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

    /**
     * 获取模块sql
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModuleSql(Request $request){
        $menuId = $request->input('menu_id');
        $sqlConfig = SqlConfig::where('menu_id',$menuId)->first();
        if(empty($sqlConfig)){
            return response()->json(['msg'=>'invalid params','code'=>400]);
        }
        return response()->json(['data'=>$sqlConfig['sql'],'code'=>200]);
    }

}
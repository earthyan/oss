<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/28
 * Time: 10:25
 */

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Request;
use App\Models\Menu;
use App\Models\ModuleVersion;
use App\Models\SqlConfig;
use App\Models\SqlWhere;

class ModuleController extends BaseController
{
    public function __construct()
    {
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $menu = Menu::all();
        foreach ($menu as $val){
            $val['productName'] = ($val['product_id']==0)? '所有游戏':$val->product()->name;
            if(isset($val['status'])&& $val['status']){
                $val['state'] = '已发布';
            }else{
                $val['state'] = '未发布';
            }
        }
        return response()->json($menu);
    }

    /***
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPageAction(Request $request){
        try{
            $key = //当前游戏的key
            $product_id = //当前游戏product_id
            $parent_id = $request->input('parent_id');
            $page_name = $request->input('page_name');

            $menu = new Menu();
            $menu->key = $key;
            $menu->product_id = $product_id;
            $menu->parent_id = $parent_id;
            $menu->page_name = $page_name;
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
            if($request->has('id')){
                $menu_id  = $request->input('id');
                $menu = Menu::find($menu_id);
                $method = $menu->method;
                $sql = SqlConfig::where('method',$method)->first();
                $version = ModuleVersion::where('menu_id',$menu_id);
                $data = array(
                    'menu'=>$menu,
                    'sql'=>$sql,
                    'version'=>$version,
                );
            }
            return response()->json($data);
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
            if($request->has('id')){
                $menu = Menu::find($request->input('id'));
            }else{
                $menu = new Menu();
            }
            $menu->product_id = $request->input('product_id');
            $menu->parent_id = $request->input('parent_id');
            $menu->page_name = $request->input('page_name');
            $menu->module_name = $request->input('module_name');
            $menu->status = 0;
            $menu->key = $request->input('key');
            $menu->type = 2; //2 表示模块
            $menu->save();

            //参数配置
            if($request->has('config_id')){
                $sqlConfig = SqlConfig::find($request->input('config_id'));
            }else{
                $sqlConfig = new SqlConfig();
            }
            $sqlConfig->method = $request->input('method');
            $sqlConfig->sql = $request->input('sql');
            $sqlConfig->head = json_encode(
                array(
                    'key'=>$request->input('key'),
                    'desc'=>serialize($request->input('desc')),
                    'type'=>$request->input('type'),
                    'chart'=>$request->has('chart')?$request->input('chart'):'',
                    'ext'=>$request->has('ext')? $request->input('ext'):'',
                ),JSON_UNESCAPED_UNICODE
            );
            $sqlConfig->save();

            //历史版本
            $lastest = ModuleVersion::where('menu_id',$request->input('id'))->orderBy('id','DESC')->first();
            $version = new ModuleVersion();
            $version->operation = session('user');//操作人员
            $version->version = !empty($lastest)? $lastest['version']+1: 0;
            $version->menu_id = $request->input('id');
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
                $sqlWhere = SqlWhere::find($request->id);
            }else{
                $sqlWhere = new SqlWhere();
            }
            $sqlWhere->key = $request->input('key');
            $sqlWhere->name =  $request->input('name');
            $sqlWhere->show_type = $request->input('show_type');
            $sqlWhere->return_type = $request->input('return_type');
            $sqlWhere->content = $request->input('content');
            $sqlWhere->explain = $request->input('explain');
            $sqlWhere->product = $request->input('product');
            $sqlWhere->save();
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
            $menu_id = $request->input('id');
            $menu = Menu::find($menu_id);
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
            $menu_id = $request->input('menu_id');
            $menu = Menu::find($menu_id);
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
            $menu_id = $request->input('menu_id');//当前的模块
            $menu = Menu::find($menu_id);


            $new_menu = new Menu();
            $new_menu->product_id = $request->input('product_id');
            $new_menu->parent_id = $request->input('parent_id');
            $new_menu->page_name = $menu->page_name;
            $new_menu->module_name = $menu->module_name;
            $new_menu->status = 0;
            $new_menu->key = $request->input('key');
            $new_menu->type = 2; //2 表示模块
            $new_menu->save();
            return response()->json(['msg'=>'success','code'=>200]);
        }catch (\Exception $e){
            return response()->json(['msg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

    //恢复历史版本模块
    public function recover(Request $request){
        try{
            $version_id = $request->input('version_id');
            $version = ModuleVersion::find($version_id);
            $version->operation = session('user');//操作人员
            $version->action_time = time();
            $version->save();
            return response()->json(['msg'=>'success','code'=>200]);
        }catch (\Exception $e){
            return response()->json(['msg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

}
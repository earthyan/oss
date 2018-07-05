<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/5
 * Time: 13:58
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\BaseController;
use App\Models\Tag;
use Illuminate\Http\Request;
use Mockery\Exception;

class TagController extends BaseController
{

    public function __construct()
    {
    }

    /***
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
        if(!$request->has('menu_id')){
            return response()->json(['msg'=>'invalid params','code'=>400]);
        }
        $tags = Tag::where('menu_id',$request->input('menu_id'))->get()->toArray();
        return response()->json(['data'=>$tags,'code'=>200]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function addTagAction(Request $request){
        try{
            $tag = new Tag();
            $tag->content = $request->input('content');
            $tag->menu_id = $request->input('menu_id');
            $tag->operator = $request->user()->user;// or name
            $tag->tag_time = time();
            $tag->save();
            return response()->json(['msg'=>'success','code'=>200]);
        }catch(Exception $e){
            return response()->json(['msg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

}
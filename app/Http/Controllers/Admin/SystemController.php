<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/13
 * Time: 23:43
 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Config;

class SystemController extends BaseController
{


    /*
     * 系统概括面板
     * @date: 2019-6-14
     * @author: yyyvy <76836785@qq.com>
     */
    public function panel()
    {
        $config = config('cuteone');
        return view('admin.system.panel', compact('config'), ['top_nav'=>'system', 'activity_nav'=>'system_panel']);
    }


    /*
     * 系统设置
     * @date: 2019-6-14
     * @author: yyyvy <76836785@qq.com>
     */
    public function setting(Request $request, Config $Config)
    {
        if($request->isMethod('get')){
            $config_info = $Config::query()->get()->toArray();
            $data = array();
            foreach ($config_info as $v){
                $data[$v['name']] = $v['value'];
            }
            return view('admin.system.setting', compact('data'), ['top_nav'=>'system', 'activity_nav'=>'system_setting']);
        }else{
            $data = $request->all();
            unset($data['_token']);
            if ($data['password'] == null){
                unset($data['password']);
            }else{
                $data['password'] = Hash::make($data['password']);
            }
            $data['toggle_web_site'] = isset($data['toggle_web_site']) ? 1 : 0;
            foreach ($data as $key=>$v){
                $Config::query()->where('name', $key)->update(['value'=>$v]);
            }
            return response()->json(['code' => 0, 'msg' => '完成！']);
        }
    }


    /*
     * 前端设置
     * @date: 2019-6-14
     * @author: yyyvy <76836785@qq.com>
     */
    public function front(Request $request, Config $Config)
    {
        if($request->isMethod('get')){
            $config_info = $Config::query()->get()->toArray();
            $data = array();
            foreach ($config_info as $v){
                $data[$v['name']] = $v['value'];
            }
            return view('admin.system.front', compact('data'), ['top_nav'=>'system', 'activity_nav'=>'system_front']);
        }else{
            $data = $request->all();
            unset($data['_token']);
            $data['search_type'] = isset($data['search_type']) ? 1 : 0;
            $data['is_music'] = isset($data['is_music']) ? 1 : 0;
            foreach ($data as $key=>$v){
                $Config::query()->where('name', $key)->update(['value'=>$v]);
            }
            return response()->json(['code' => 0, 'msg' => '完成！']);
        }
    }


    /*
     * 上传图片
     * @date: 2019-6-14
     * @author: yyyvy <76836785@qq.com>
     */
    public function upload(Request $request)
    {
        //上传图片具体操作
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES["file"]["tmp_name"];

        $date = date('Ymd');
        $file_name_arr = explode('.', $file_name);
        $new_file_name = date('YmdHis') . '.' . $file_name_arr[1];
        $path = "upload/".$date."/";
        $file_path = $path . $new_file_name;
        if (file_exists($file_path)) {
            return response()->json(['code' => 1, 'msg' => '此文件已经存在啦！']);
        } else {
            //TODO 判断当前的目录是否存在，若不存在就新建一个!
            if (!is_dir($path)){mkdir($path,0777);}
            $upload_result = move_uploaded_file($file_tmp, $file_path);
            //此函数只支持 HTTP POST 上传的文件
            if ($upload_result) {
                return response()->json(['code' => 0, 'msg' => '文件上传成功！', 'data'=> ['src'=>'/'.$file_path]]);
            } else {
                return response()->json(['code' => 1, 'msg' => '文件上传失败！']);
            }
        }

    }





}
<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\BaseController;
use App\Models\Disks;
use App\Models\Menus;

class DiskController extends BaseController
{

    /*
     * 网盘管理
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     */
    public function index(Request $request){
        if($request->all()){
            $perLimit = $request->input('limit',10); //条数
            $query = Disks::query()->orderBy('created_at', 'desc');
            $result = $query->paginate($perLimit);
            $result =  collect($result)->toArray();
            foreach ($result["data"] as &$row){
                $cache_db = DB::table('cache_'.$row['id']);
                $row["count"] = $cache_db->count();
                if ($row["types"] == 1){
                    $row["types"] = "国际版";
                }else{
                    $row["types"] = "世纪互联";
                }
            }
            $json_data = [
                "code" => 0,
                "msg" => "",
                "count" => $result["total"],
                "data" => $result["data"]
            ];
            return $json_data;
        }else{
            $disk_free = Disks::query()->get()->toArray();
            if ($disk_free) {
                $free = 1;
            }else{
                $free = 0;
            }
            return view('admin.disk.index', ['free'=>$free, 'top_nav'=>'disk', 'activity_nav'=>'disk_list']);
        }
    }


    /*
     * 网盘新增/编辑
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     */
    public function edit(Request $request, $id){
        $redirect_url = config('program.redirectUrl');
        if($request->isMethod('get')){
            $disk_free = Disks::query()->get()->toArray();
            if ($disk_free) {
                $free = 1;
            } else {
                $free = 0;
            }
            if($id){
                $data = Disks::query()->where('id', $id)->first()->toArray();
                $free = 0;
            }else{
                $data = [
                    'id' => 0,
                    'title' => '',
                    'description' => '',
                    'client_secret' => '',
                    'client_id' => '',
                    'types' => 1,
                    'other' => ''
                ];
            }
            return view('admin.disk.edit', compact('free', 'data', 'redirect_url'));
        }else{
            $data = $request->all();
            if ($data['other'] == null){
                $data['other'] = '';
            }
            if($id){
                if($data['code']){
                    if ($data['types'] == '1'){
                        $url = config('program.baseAuthUrl').'/common/oauth2/v2.0/token';
                        $AuthData = 'client_id='.$data['client_id'].'&redirect_uri='.$redirect_url.'&client_secret='.$data['client_secret'].'&code='.$data['code'].'&grant_type=authorization_code';
                    }else{
                        $url = config('program.chinaAuthUrl').'/common/oauth2/token';
                        $AuthData = 'client_id='.$data['client_id'].'&redirect_uri='.$redirect_url.'&client_secret='.$data['client_secret'].'&code='.$data['code'].'&grant_type=authorization_code&resource=00000003-0000-0ff1-ce00-000000000000';
                    }
                    $headers = array('Content-Type:application/x-www-form-urlencoded');
                    $http_res = http_request($url, $headers, $AuthData);
                    if(array_key_exists('error', $http_res)){
                        return response()->json(['code' => 1, 'msg' => $http_res['error_description']]);
                    }
                    $data['token'] = json_encode($http_res);
                    unset($data['_token']);
                    unset($data['code']);
                }
                Disks::query()->where('id', $data['id'])->update($data);
                reacquireToken($data['id']);
            }else{
                if ($data['types'] == '1'){
                    $url = config('program.baseAuthUrl').'/common/oauth2/v2.0/token';
                    $AuthData = 'client_id='.$data['client_id'].'&redirect_uri='.$redirect_url.'&client_secret='.$data['client_secret'].'&code='.$data['code'].'&grant_type=authorization_code';
                }else{
                    $url = config('program.chinaAuthUrl').'/common/oauth2/token';
                    $AuthData = 'client_id='.$data['client_id'].'&redirect_uri='.$redirect_url.'&client_secret='.$data['client_secret'].'&code='.$data['code'].'&grant_type=authorization_code&resource=00000003-0000-0ff1-ce00-000000000000';
                }
                $headers = array('Content-Type:application/x-www-form-urlencoded');
                $http_res = http_request($url, $headers, $AuthData);
                if(array_key_exists('error', $http_res)){
                    return response()->json(['code' => 1, 'msg' => $http_res['error_description']]);
                }
                $data['token'] = json_encode(json_encode($http_res));
                unset($data['id'], $data['_token'], $data['code']);
                $DisksModel = new Disks();
                foreach ($data as $key=>$row){
                    $DisksModel[$key] = $row;
                }
                $DisksModel->save();
                $id = $DisksModel->id;
                reacquireToken($id);
                createCacheTable($id);
                Menus::query()->insert(['position'=>0, 'title'=>$data['title'], 'url'=>'/disk/'.$id, 'top_nav'=>'disk', 'type'=>1, 'type_name'=>$id, 'activate'=>0, 'sort'=>0, 'target'=>0, 'status'=>1]);
            }
            return response()->json(['code' => 0, 'msg' => '完成！']);
        }
    }


    /*
     * 网盘删除
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     */
    public function del($id){
        $disk_data = Disks::find($id);
        $disk_data->delete();
        dropCacheTable($id);
        return response()->json(['code' => 0, 'msg' => '完成！']);
    }


    /*
     * 网盘文件列表
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     */
    public function file_list(Request $request, $id){
        $path = $request->input('path',''); //路径
        $path = urldecode($path);
        $current_url = '/admin/disk/file_list/'.$id.'/?path='.$path;
        $data_list = getNewFileList($id, $path);
        $data = $data_list['data'];
        foreach ($data as &$v) {
            $v['size'] = sizeCov($v['size']);
            $v['lastModifiedDateTime'] = utcToLocal($v['lastModifiedDateTime']);
        }
        return view('admin.disk.file_list', compact('data', 'current_url'), ['top_nav'=>'disk', 'activity_nav'=>'disk_list']);
    }


    /*
     * 更新网盘缓存
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     * @param int $id 网盘id
     * @param int $types 类型 1是普通更新，2是增量更新
     */
    public function cache(Request $request){
        $id = $request->input('id');
        $types = $request->input('types');
//        Artisan::call('command:DiskCache', [
//            'method' => 'cache', 'disk_id' => $id, 'types' => $types
//        ]);
        Artisan::queue('command:DiskCache', [
            'method' => 'cache', 'disk_id' => $id, 'types' => $types
        ]);
        return response()->json(['code' => 0, 'msg' => '完成！']);
    }
}

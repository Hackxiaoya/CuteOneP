<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Disks;
use App\Models\AuthorRules;

class AuthorController extends BaseController
{

    /*
     * 权限管理
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     */
    public function index(Request $request){
        if($request->all()){
            $perLimit = $request->input('limit',10); //条数
            $query = AuthorRules::query()->orderBy('id', 'desc');
            $result = $query->paginate($perLimit);
            $result =  collect($result)->toArray();
            foreach ($result["data"] as &$row){
                $disk_info = getDiskInfo($row['disk_id']);
                $row["disk_name"] = $disk_info ? $disk_info['title'] : '';
            }
            $json_data = [
                "code" => 0,
                "msg" => "",
                "count" => $result["total"],
                "data" => $result["data"]
            ];
            return $json_data;
        }else{
            return view('admin.author.index', ['top_nav'=>'author', 'activity_nav'=>'index']);
        }
    }


    /*
     * 权限新增/编辑
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     */
    public function edit(Request $request, $id){
        if($request->isMethod('get')){
            if($id){
                $data = AuthorRules::query()->where('id', $id)->first()->toArray();
            }else{
                $data = [
                    'id' => 0,
                    'title' => '',
                    'path' => '',
                    'password' => '',
                    'disk_id' => '',
                    'login_hide' => 1
                ];
            }
            $disk_list = getDiskList();
            return view('admin.author.edit', compact('data', 'disk_list'));
        }else{
            $data = $request->all();
            unset($data['_token']);
            if($id){
                AuthorRules::query()->where('id', $data['id'])->update($data);
            }else{
                unset($data['id']);
                $AuthorRulesModel = new AuthorRules();
                foreach ($data as $key=>$row){
                    $AuthorRulesModel[$key] = $row;
                }
                $AuthorRulesModel->save();
            }
            return response()->json(['code' => 0, 'msg' => '完成！']);
        }
    }


    /*
     * 权限删除
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     */
    public function del($id){
        $disk_data = AuthorRules::find($id);
        $disk_data->delete();
        return response()->json(['code' => 0, 'msg' => '完成！']);
    }


}

<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/14
 * Time: 4:56
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Menus;

class MenuController extends BaseController
{

    /*
     * 菜单管理
     * @date: 2019-6-14
     * @author: yyyvy <76836785@qq.com>
     * @param int $position 菜单位置，0是前端 1是后台
     */
    public function index(Request $request, $position){
        if($request->all()){
            $perLimit = $request->input('limit',10); //条数
            $query = Menus::query()->where('position', $position)->orderBy('id', 'desc');
            $result = $query->paginate($perLimit);
            $result =  collect($result)->toArray();
            $type = ['自定义', '网盘'];
            foreach ($result["data"] as &$v){
                $v['type'] = $type[$v['type']];
                $v['activate'] = $v['activate']==0 ? '<span class="layui-btn layui-btn-primary layui-btn-xs">否</span>' : '<span class="layui-btn layui-btn-normal layui-btn-xs">是</span>';
                $v['status'] = $v['status']==0 ? '<span class="layui-btn layui-btn-primary layui-btn-xs">否</span>' : '<span class="layui-btn layui-btn-normal layui-btn-xs">是</span>';
            }
            $json_data = [
                "code" => 0,
                "msg" => "",
                "count" => $result["total"],
                "data" => $result["data"]
            ];
            return $json_data;
        }else{
            $activity_nav = $position==0 ? 'menus_in_list' : 'menus_out_list';
            $position_name = $position==0 ? '前台' : '后台';
            return view('admin.menu.list', compact('position', 'position_name'), ['top_nav'=>'menus', 'activity_nav'=>$activity_nav]);
        }
    }



    /*
     * 菜单新增/编辑
     * @date: 2019-6-14
     * @author: yyyvy <76836785@qq.com>
     */
    public function edit(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            if ($id) {
                $data = Menus::query()->where('id', $id)->first()->toArray();
            } else {
                $data = [
                    'id' => 0,
                    'title' => '',
                    'url' => '',
                    'position' => 0,
                    'type' => 0,
                    'sort' => 0,
                    'target' => 0
                ];
            }
            return view('admin.menu.edit', compact('data'));
        } else {
            $data = $request->all();
            unset($data['_token']);
            if($id){
                $data['activate'] = isset($data['activate']) ? 1 : 0;
                $data['status'] = isset($data['status']) ? 1 : 0;
                Menus::query()->where('id', $data['id'])->update($data);
            }else{
                $data['activate'] = isset($data['activate']) ? 1 : 0;
                $data['status'] = isset($data['status']) ? 1 : 0;
                //dd($data);
                $MenusModel = new Menus();
                foreach ($data as $key=>$row){
                    $MenusModel[$key] = $row;
                }
                $MenusModel->save();
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
        $disk_data = Menus::find($id);
        $disk_data->delete();
        return response()->json(['code' => 0, 'msg' => '完成！']);
    }

}
<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Disks;

class DiskController extends Controller
{

    public function __construct(){
        $request = request()->all();
        $path = isset($request['path']) ? $request['path'] : '';
        $path = urldecode(urldecode($path));
        $crumbs_list = array();
        if ($path) {
            $path = explode("/",$path);
            unset($path[0]);
            $temp_path = '';
            foreach ($path as $v) {
                $temp_path .= $v;
                array_push($crumbs_list, ['path'=>$temp_path, 'name'=>$v]);
                $temp_path .= '/';
            }
        }
        view()->share('crumbs_list', $crumbs_list);

        $sortTable = isset($request['sortTable']) ? $request['sortTable'] : 'lastModifiedDateTime';
        $sortType = isset($request['sortType']) ? $request['sortType'] : 'more';
        view()->share(['sortTable'=>$sortTable, 'sortType'=>$sortType]);
    }


    /*
     * 文件夹权限拦截器
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     */
    public function authorJudge($request, $disk_id){
        $path = $request->input('path', '');
        $path = urldecode(urldecode($path));
        if ($path) {
            $res = Disks::authorJudge($path, $disk_id);
            return $res;
        }

    }


    /*
     * 文件夹密码认证
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     */
    public function approve(Request $request){
        $path = $request->input('path', '');
        $path = urldecode(urldecode($path));
        $disk_id = $request->input('disk_id', '');
        $password = $request->input('password', '');
        $res = Disks::author_password($path, $disk_id, $password);
        if ($res) {
            $request->session()->put($path, $password);
            return response()->json(['code'=>0, 'msg'=>'密码正确！']);
        } else {
            return response()->json(['code'=>1, 'msg'=>'密码错误！']);
        }
    }


    /*
     * 网盘列表数据
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     */
    public function index(Request $request, $disk_id){
        $Disks = new Disks();
        $path = $request->input('path', '');
        $path = urldecode($path);
        //dd($path);
        $offset = $request->input('page', 1);
        $search = $request->input('search', '');
        $search = urldecode($search);
        $sortTable = $request->input('sortTable', 'lastModifiedDateTime');
        $sortType = $request->input('sortType', 'more');
        $current_url = '/disk/'.$disk_id.'/?path='.$path;
        $crumbs_url = '/disk/'.$disk_id.'/?path=';

        // 文件夹权限拦截器
        $author_res = self::authorJudge($request, $disk_id);
        if ($author_res) {
            return view('front.default.disk.author', compact('disk_id', 'crumbs_url', 'current_url', 'path'));
        }

        $data = $Disks->getDiskCacheList($disk_id, $path, $search, $offset, $sortTable, $sortType);
        foreach ($data['data'] as &$v) $v->size = sizeCov($v->size);
        // dd($data);
        return view('front.default.disk.index', compact('disk_id', 'data', 'crumbs_url', 'current_url'));
    }


    /*
     * 获取缩略图
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     */
    public function thumbnails($disk_id, $file_id){
        $file_info = fileCacheUrl($disk_id, $file_id);
        header( "Content-Disposition: attachment;  filename=".$file_info['name']);
        header('Location: '. $file_info['thumbnails']);
    }


    /*
     * 下载文件
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     */
    public function downFile($disk_id, $file_id){
        $file_info = fileCacheUrl($disk_id, $file_id);
        header( "Content-Disposition: attachment;  filename=".$file_info['name']);
        header('Location: '. $file_info['downloadUrl']);
    }


    /*
     * 文件信息
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     */
    public function downloadInfo($disk_id, $file_id){
        $file_info = fileCacheUrl($disk_id, $file_id);
        return response()->json(['code' => 0, 'msg' => '完成！', 'data' => $file_info]);
    }



    /*
     * 视频播放器弹出层
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     */
    public function popVideo($disk_id, $file_id){
        $data = fileCacheUrl($disk_id, $file_id);
        $data['disk_id'] = $disk_id;
        return view('front.default.disk.pop_video', compact('data'));
    }


    /*
     * 视频播放器独立页面
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     */
    public function video($disk_id, $file_id){
        $data = fileCacheUrl($disk_id, $file_id);
        $data['disk_id'] = $disk_id;
        return view('front.default.disk.video', compact('disk_id', 'data'));
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\Config;
use App\Models\AuthorRules;

class Disks extends Model
{


    /*
     * 文件夹权限拦截器
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     */
    public static function authorJudge($path, $disk_id){
        $res = AuthorRules::query()->where([['path', $path],['disk_id', $disk_id]])->first();
        if ($res) {
            if (session($path) == $res['password']) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }


    /*
    * 文件夹密码认证
    * @date: 2019-6-15
    * @author: yyyvy <76836785@qq.com>
    */
    public static function author_password($path, $disk_id, $password){
        $res = AuthorRules::query()->where([['path', $path],['disk_id', $disk_id]])->first();
        if ($password == $res['password']) {
            return true;
        } else {
            return false;
        }
    }



    /**
     * 获取缓存文件列表
     * @Author: yyyvy <76836785@qq.com>
     * @Description:
     * @Time: 2019-6-8
     * @param int $disk_id 网盘id
     * @param string $path 路径
     * @param string $search：搜索词
     * @param int $offset：页码
     * @param string $sortTable：排序字段
     * @param string $sortType：排序类型
     */
    public function getDiskCacheList($disk_id, $path, $search, $offset, $sortTable, $sortType)
    {
        $Config = new Config();
        $result = ["data" => array(), "count" => ""];
        //dd($sortTable);
        if ($sortType == 'more'){
            $sortType = 'DESC';
        }else{
            $sortType = 'ASC';
        }
        if ($search){
            $folder_list = array();
            $data_list = DB::table('cache_' . $disk_id)->where([['name', 'like', '%'.$search.'%'], ['file_type', '!=', 'folder']])->orderBy($sortTable, $sortType)->get()->toArray();
            $result["count"] = DB::table('cache_' . $disk_id)->where([['name', 'like', '%'.$search.'%'], ['file_type', '!=', 'folder']])->orderBy($sortTable, $sortType)->count();
        }else{
            $folder_list = DB::table('cache_' . $disk_id)->where([['path', $path], ['file_type', 'folder']])->orderBy($sortTable, $sortType)->get()->toArray();
            $data_list = DB::table('cache_' . $disk_id)->where([['path', $path], ['file_type', '!=', 'folder']])->orderBy($sortTable, $sortType)->get()->toArray();
            $result["count"] = DB::table('cache_' . $disk_id)->where('path', $path)->count();
        }

        $result["data"] = array_merge($folder_list, $data_list);
        $limit = $Config->webConfig()['page_number'];
        //$limit = 1;
        $result = paginates($result["data"], $limit, $offset);
        // dd($result);
        return $result;
    }
}


<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/8
 * Time: 8:13
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Disks;

/**
 * UTC时间转换
 * @param utc $utc UTC时间
 */
function utcToLocal($utc){
    return date("Y-m-d H:i:s",strtotime($utc));
}


/**
 * 文件大小转换
 * @param int $size 文件尺寸
 */
function sizeCov($size){
    $units = array(' B', ' KB', ' MB', ' GB', ' TB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    return round($size, 2).$units[$i];
}


/**
 * 公用的方法  curl请求  返回json数据
 * @param string $url URL
 * @param array $headers 头部信息
 * @param array $data 数据
 */
function http_request($url, $headers, $data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $output = curl_exec($curl);
    $errno = curl_errno($curl);
    curl_close($curl);
    return json_decode($output, true);
}


/**
 * 获取网盘列表
 * @Author: yyyvy <76836785@qq.com>
 * @Description:
 * @Time: 2019-6-15
 */
function getDiskList(){
    return Disks::query()->get()->toArray();
}


/**
 * 获取网盘信息
 * @Author: yyyvy <76836785@qq.com>
 * @Description:
 * @Time: 2019-6-15
 * @param int $id 网盘ID
 */
function getDiskInfo($id){
    return Disks::query()->where('id', $id)->first();
}


/**
 * 公用的方法  重新获取token
 * @param int $disk_id 网盘id
 * @param int $times 重试次数
 */
function reacquireToken($disk_id, $times=0){
    $disk_info = Disks::query()->where('id', $disk_id)->first();
    $token = json_decode(json_decode($disk_info['token']), true);
    $redirect_url = config('program.redirectUrl');
    $AuthData = 'client_id='.$disk_info['client_id'].'&redirect_uri='.$redirect_url.'&client_secret='.$disk_info['client_secret'].'&refresh_token='.$token['refresh_token'].'&grant_type=refresh_token';
    $headers = array('Content-Type:application/x-www-form-urlencoded');
    if ($disk_info['types'] == 1){
        $url = config('program.baseAuthUrl').'/common/oauth2/v2.0/token';
    }else{
        $url = config('program.chinaAuthUrl').'/common/oauth2/token';
        $AuthData = $AuthData.'&resource=https://'.$disk_info['other'].'-my.sharepoint.cn/';
    }
    $http_res = http_request($url, $headers, $AuthData);
    if(array_key_exists('error', $http_res)){
        if($times < 4){
            return reacquireToken($disk_id, $times++);
        }
    }else{
        $data['id'] = $disk_info['id'];
        $data['token'] = json_encode(json_encode($http_res));
        Disks::query()->where('id', $data['id'])->update($data);
    }
}


/**
 * 创建网盘缓存数据表
 * @Author: yyyvy <76836785@qq.com>
 * @Description:
 * @Time: 2019-6-8
 * @param int $disk_id 网盘id
 */
function createCacheTable($disk_id){
    Schema::create('cache_'.$disk_id, function($table)
    {
        $table->increments('id');
        $table->string('file_id');
        $table->string('parentReference')->nullable();
        $table->string('name');
        $table->string('file_type');
        $table->string('path')->nullable();
        $table->string('size');
        $table->string('createdDateTime');
        $table->string('lastModifiedDateTime');
        $table->text('thumbnails')->nullable();
        $table->text('downloadUrl')->nullable();
        $table->integer('timeout')->nullable();
    });
}


/**
 * 删除网盘缓存数据表
 * @Author: yyyvy <76836785@qq.com>
 * @Description:
 * @Time: 2019-6-8
 * @param int $disk_id 网盘id
 */
function dropCacheTable($disk_id){
    Schema::drop('cache_'.$disk_id);
}


/**
 * 拉取列表信息到缓存数据库
 * @Author: yyyvy <76836785@qq.com>
 * @Description:
 * @Time: 2019-6-8
 * @param int $disk_id 网盘id
 * @param int $types 类型 1是普通更新，2是增量更新
 * @param string $path 路径
 */
function refreshCache($disk_id, $types, $path){
    set_time_limit(0);  // 防止脚本超时, 有些虚拟主机可能会禁用此函数
    $data_list = getNewFileList($disk_id, $path)['data'];
    foreach ($data_list as $f){
        $cache_db = DB::table('cache_'.$disk_id);
        $data = array(
            'file_id' => $f['id'],
            'name' => $f['name'],
            'path' => str_replace('/drive/root:', '', $f['parentReference']["path"]),
            'size' => $f['size'],
            'createdDateTime' => utcToLocal($f['createdDateTime']),
            'lastModifiedDateTime' => utcToLocal($f['lastModifiedDateTime']),
            'thumbnails' => '',
            'downloadUrl' => ''
        );
        if(array_key_exists('folder', $f)) {
            $data['file_type'] = 'folder';
            $query = $cache_db->where([
                ['file_id', '=', $data['file_id']],
                ['path', '=', $data['path']]
            ])->first();
            if ($query){
                $cache_db->where('id', $query->id)->update($data);
                if ($types == 2){
                    if ($query->size != $f['size']){
                        refreshCache($disk_id, $types, '/'.$path.'/'.$f['name']);
                    }
                } else {
                    refreshCache($disk_id, $types, '/'.$path.'/'.$f['name']);
                }
            }else{
                $cache_db->insertGetId($data);
                refreshCache($disk_id, $types, $path.'/'.$f['name']);
            }
        }else{
            if(array_key_exists('@microsoft.graph.downloadUrl', $f)) {
                $downloadUrl = $f['@microsoft.graph.downloadUrl'];
            }else{
                $downloadUrl = $f['@content.downloadUrl'];
            }
            if(array_key_exists('thumbnails', $f)) {
                //dd($f);
                $thumbnails = isset($f["thumbnails"][0]["large"]["url"]) ? $f["thumbnails"][0]["large"]["url"] : $downloadUrl;
            }else{
                $thumbnails = $downloadUrl;
            }
            $data['file_type'] = $f['file']['mimeType'];
            $data['thumbnails'] = $thumbnails;
            $data['downloadUrl'] = $downloadUrl;
            $data['timeout'] = time()+300;
            $query = $cache_db->where([
                ['file_id', '=', $data['file_id']],
                ['path', '=', $data['path']]
            ])->first();
            if ($query){
                $cache_db->where('file_id', $data['file_id'])->update($data);
            }else{
                $cache_db->insertGetId($data);
            }
        }
    }
    #return "更新缓存完成！";
}


/**
 * 获取文件缓存下载地址
 * @Author: yyyvy <76836785@qq.com>
 * @Description:
 * @Time: 2019-6-8
 * @param int $disk_id 网盘id
 * @param string $file_id 资源id
 */
function fileCacheUrl($disk_id, $file_id){
    $cache_db = DB::table('cache_'.$disk_id);
    $query = $cache_db->where('file_id', $file_id)->first();
    if ($query){
        if ($query->timeout <= time()){
            return getDownloadUrl($disk_id, $file_id);
        }else{
            return array('file_id' => $query->file_id, 'name' => $query->name, 'size' => $query->size, 'thumbnails' => $query->thumbnails, 'downloadUrl' => $query->downloadUrl);
        }
    }else{
        return getDownloadUrl($disk_id, $file_id);
    }
}


/**
 * 从新拉取真实地址
 * @Author: yyyvy <76836785@qq.com>
 * @Description:
 * @Time: 2019-6-8
 * @param int $disk_id 网盘id
 * @param string $file_id 资源id
 * @param int $times 重试次数
 */
function getDownloadUrl($disk_id, $file_id, $times=0){
    $disk_info = Disks::query()->where('id', $disk_id)->first();
    $token = json_decode(json_decode($disk_info['token']), true);
    $headers = array('Authorization: Bearer '.$token['access_token']);
    if ($disk_info['types'] == 1){
        $url = config('program.appUrl').'/v1.0/me/drive/items/'.$file_id;
    }else{
        $url = 'https://'.$disk_info['other'].'-my.sharepoint.cn/_api/v2.0/me/drive/items/'.$file_id;
    }
    $http_res = http_request($url, $headers);
    if(array_key_exists('error', $http_res)) {
        if ($times < 4) {
            reacquireToken($disk_id);
            return getDownloadUrl($disk_id, $file_id, $times++);
        }
    }else{
        $data = array('timeout' => time()+300);
        if(array_key_exists('@microsoft.graph.downloadUrl', $http_res)) {
            $data['downloadUrl'] = $http_res['@microsoft.graph.downloadUrl'];
        }else{
            $data['downloadUrl'] = $http_res['@content.downloadUrl'];
        }
        if(array_key_exists('thumbnails', $http_res)) {
            $data['thumbnails'] = $http_res["thumbnails"][0]["large"]["url"];
        }else{
            $data['thumbnails'] = $data['downloadUrl'];
        }
        $query = DB::table('cache_'.$disk_id)->where('file_id', $http_res['id'])->first();
        if($query){
            DB::table('cache_'.$disk_id)->where('file_id', $http_res['id'])->update($data);
        }else{
            $cache_db = DB::table('cache_'.$disk_id);
            $cache_db->file_id = $http_res['id'];
            $cache_db->parentReference = $http_res['parentReference'];
            $cache_db->name = $http_res['name'];
            $cache_db->file_type = $http_res['file_type'];
            $cache_db->path = $http_res['path'];
            $cache_db->size = $http_res['size'];
            $cache_db->createdDateTime = utcToLocal($http_res['createdDateTime']);
            $cache_db->lastModifiedDateTime = utcToLocal($http_res['lastModifiedDateTime']);
            $cache_db->thumbnails = $data['thumbnails'];
            $cache_db->downloadUrl = $data['downloadUrl'];
            $cache_db->timeout = $data['timeout'];
            $cache_db->save();
        }
    }
    return array('file_id' => $http_res['id'], 'name' => $http_res['name'], 'size' => $http_res['size'], 'thumbnails' => $data['thumbnails'], 'downloadUrl' => $data['downloadUrl']);
}


/**
 * 获取网盘最新数据列表
 * @Author: yyyvy <76836785@qq.com>
 * @Description:
 * @Time: 2019-6-8
 * @param int $disk_id 网盘id
 * @param string $path 路径
 * @param int $times 重试次数
 */
function getNewFileList($disk_id, $path='', $times=0){
    $disk_info = Disks::query()->where('id', $disk_id)->first();
    $token = json_decode(json_decode($disk_info['token']), true);
    $headers = array('Content-Type: application/json','Authorization: Bearer '.$token['access_token']);
    if ($disk_info['types'] == 1){
        $url = config('program.appUrl').'/v1.0/me/drive';
    }else{
        $url = 'https://'.$disk_info['other'].'-my.sharepoint.cn/_api/v2.0/me/drive';
    }
    // print_r($path);  // 调试更新缓存，开启这一段即可
    $path = rawurlencode($path);
    //dd($path);
    if ($path){
        $url = $url.'/root:'.$path.':/children?expand=thumbnails';
    }else{
        $url = $url.'/root/children?expand=thumbnails';
    }
    $http_res = http_request($url, $headers);
    // dd($http_res);
    if (array_key_exists('error', $http_res)) {
        if ($times < 4) {
            reacquireToken($disk_id);
            return getNewFileList($disk_id, $path, $times++);
        }
    }else{
        if (array_key_exists('value', $http_res)) {
            $result = $http_res['value'];
            if (array_key_exists('@odata.nextLink', $http_res)) {
                $pageres = getNewFileListPage($token['access_token'], $http_res["@odata.nextLink"]);
                $result = array_merge($result, $pageres);
            }
            // 处理世纪互联地址
            if ($disk_info['types'] == 2){
                foreach ($result as &$row){
                    if (!array_key_exists('folder', $row)) {
                        $row['thumbnails']+=array('large'=>array('url'=>$row['@content.downloadUrl']));
                    }
                }
            }
            return array('code'=>true, 'msg'=>'获取成功', 'data'=>$result);
        }else{
            return getNewFileList($disk_id, $path, $times++);
        }
    }
    return 0;
}


/**
 * 获取网盘最新数据列表 - 分页获取
 * @Author: yyyvy <76836785@qq.com>
 * @Description:
 * @Time: 2019-6-8
 * @param string $token 网盘token
 * @param string $nextLink 下一页URL
 */
function getNewFileListPage($token, $nextLink){
    $headers = array('Content-Type: application/json','Authorization: Bearer '.$token);
    $http_res = http_request($nextLink, $headers);
    $total = array();
    if (array_key_exists('value', $http_res)) {
        $total += $http_res['value'];
        if (array_key_exists('@odata.nextLink', $http_res)) {
            return getNewFileListPage($token, $http_res["@odata.nextLink"]);
        }
        return $total;
    }
    return 0;
}


/**
 * 公共分页方法
 * @Author: yyyvy <76836785@qq.com>
 * @Description:
 * @Time: 2019-6-14
 * @param array $count：全部数据
 * @param int $limit：条数
 * @param int $offset：页码
 */
function paginates($count, $limit, $offset){
    $count_page = ceil(count($count)/$limit);   // 总页数
    $slice_offset = ($offset-1) * $limit;   // 起始位置
    $data = array_slice($count, $slice_offset, $limit);   // 当前页数据 = 起始位置，长度
    // dd($data);
    $all_page = []; // 页码
    if ($count_page > 10){
        if ($offset > 5){
            if ($offset+3 >= $count_page){
                if ($offset-3 >= $count_page){
                    $all_page = [1, '...', $offset, $offset+1, $offset+2, '...', $count_page];
                }else{
                    $all_page = [1, '...'];
                    $all_page = array_merge($all_page, range($offset-1, $count_page, 1));
                }
            }else{
                $all_page = [1, '...', $offset-1, $offset, $offset+1, $offset+2, $offset+3, '...', $count_page];
            }
        }else{
            $all_page = range(1, 10, 1);
            array_push($all_page, '...');
            array_push($all_page, $count_page);
        }
    }else{
        $all_page = range(1, $count_page, 1);
    }
    //dd($all_page);
    return array("data"=> $data, "pagination"=> array("count"=> $count_page, "page"=> $all_page, "now_page"=> $offset));
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/15
 * Time: 13:41
 */
namespace App\Http\Controllers\Service;
use App\Http\Controllers\Controller;
use App\Models\Menus;
use App\Models\Modules;
use App\Models\Plugins;

class ExtensionService extends Controller
{

    // TODO 公共逻辑层文件


    /**
     * 检测信息文件
     * @param  string $name 信息
     * @return [type] [description]
     * @date   2019-6-15
     * @author yyyvy <76836785@qq.com>
     */
    public function checkInfoFile($info_file='')
    {
        if (!is_file($info_file))
        {
            ['code'=>1,'msg'=>'应用信息文件不存在或文件权限不足','data'=>''];
        }
        $info_check_keys = ['name', 'title', 'description', 'author', 'version'];
        $app_info = $this->getInfoByFile($info_file);
        foreach ($info_check_keys as $value) {
            if (!array_key_exists($value, $app_info)) {
                ['code'=>1,'msg'=>'应用信息缺失','data'=>''];
            }

        }
        return ['code'=>0,'msg'=>'ok','data'=>$app_info];

    }


    /*
     * 读取插件、模块、主题 配置
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     * */
    public function getInfoByFile($info_file = ''){
        if (is_file($info_file)) {
            $info = file_get_contents($info_file);
            $info = json_decode($info,true);
            return $info;
        } else {
            return [];
        }
    }


    /*
     * 安装校验
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     * @param  int $type 类型 1模块 2插件
     * @param  string $name 唯一名称
     */
    public function installCheck($type, $name){
        $plugin_dir = app_path() . '/Http/Controllers';
        if ($type == 1) {
            $info_file = $plugin_dir . '/modules/'.$name.'/install/info.json';
        } else {
            $info_file = $plugin_dir . '/plugins/'.$name.'/install/info.json';
        }
        $app_info = $this->getInfoByFile($info_file);
        $config = config('cuteone');
        if ($config['version'] < $app_info['dependences']['core']) {
            return ['code'=>1,'msg'=>'框架版本太低，不能安装此应用！'];
        }
        foreach ($app_info['dependences']['modules'] as $m) {
            $res = $this->installCheck(1, $m);
            if (!$res) {
                return ['code'=>1,'msg'=>'请先安装 '. $m .' 模块！'];
            }
        }
        foreach ($app_info['dependences']['plugins'] as $m) {
            $res = $this->installCheck(2, $m);
            if (!$res) {
                return ['code'=>1,'msg'=>'请先安装 '. $m .' 插件！'];
            }
        }
        return ['code'=>0,'msg'=>'检测通过！'];
    }


    /**
     * 检测是否安装了某个插件 or 模块
     * @param  int $type 类型 1模块 2插件
     * @param  string $name 唯一名称
     * @return [type] [description]
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     */
    public static function checkInstall($type, $name)
    {
        if ($type == 1) {
            $res = Modules::query()->where('name', $name)->first();
            if ($res) {
                return true;
            }
        } else {
            $res = Plugins::query()->where('name', $name)->first();
            if ($res) {
                return true;
            }
        }
        return false;
    }


    /*
     * 移动static资源文件和view视图文件
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     * @param  int $type 类型 1模块 2插件
     * @param  string $name 唯一名称
     * @param  bool $status 安装/卸载 true or false
     */
    public function resourceMove($type, $name, $status){
        $obj_path = app_path() . '/Http/Controllers';
        if ($type == 1) {
            $obj_path.='/Modules/'.$name;
            $_static_path = public_path().'/modules/'.$name;
            $_views_path = base_path().'/resources/views/modules/'.$name;
            return 0;
        } else {
            $obj_path.='/Plugins/'.$name;
            $_static_path = public_path().'/plugins/'.$name;
            $_views_path = base_path().'/resources/views/plugins/'.$name;
        }

        //防止路径报错，前先清理静态资源目录
        if (is_dir($_static_path)) {
            $this->deldir($_static_path);
        }

        // 复制资源文件
        if ($status) {
            $static_path = $obj_path.'/static';
            if (is_dir($static_path)) {
                if (!$this->copydirs($static_path,$_static_path)) {
                    return ['code'=>1,'msg'=>'应用静态资源移动失败！'];
                }
            }
        }

        //防止路径报错，前先清理模板文件目录
        if (is_dir($_views_path)) {
            $this->deldir($_views_path);
        }
        // 复制模板文件
        if ($status) {
            $views_path = $obj_path.'/view';
            if (is_dir($views_path)) {
                if (!$this->copydirs($views_path,$_views_path)) {
                    return ['code'=>1,'msg'=>'模板资源移动失败！'];
                }
            }
        }
        return ['code'=>0,'msg'=>'成功'];
    }


    /*
     * 写入/删除菜单
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     * @param  int $type 类型 1模块 2插件
     * @param  string $name 唯一名称
     * @param  bool $status 写入、删除
     */
    public function menus($type, $name, $status){
        $plugin_dir = app_path() . '/Http/Controllers';
        if ($type == 1) {
            $type = 2;
            $info_file = $plugin_dir . '/modules/'.$name.'/install/menus.json';
        } else {
            $type = 3;
            $info_file = $plugin_dir . '/plugins/'.$name.'/install/menus.json';
        }
        if ($status) {
            Menus::query()->where('type_name', $name)->delete();    // 先清理一次
            if (file_exists($info_file)) {
                $app_info = $this->getInfoByFile($info_file);
                foreach ($app_info as $key=>$v) {
                    if ($key == 'admin_menus') {
                        $position = 1;
                    } else {
                        $position = 0;
                    }
                    $data = [
                        'title' => $app_info['admin_menus']['title'],
                        'position' => $position,
                        'url' => $app_info['admin_menus']['url'],
                        'top_nav' => $app_info['admin_menus']['top_nav'],
                        'activity_nav' => $app_info['admin_menus']['activity_nav'],
                        'icon' => $app_info['admin_menus']['icon'],
                        'type' => $type,
                        'type_name' => $name,
                        'updated_at' => date("Y-m-d H-i-s",time()),
                        'created_at' => date("Y-m-d H-i-s",time())
                    ];
                    $pid = Menus::query()->insertGetId($data);
                    foreach ($app_info[$key]['children'] as $c) {
                        $c_data = [
                            'title' => $c['title'],
                            'pid' => $pid,
                            'position' => $position,
                            'url' => $c['url'],
                            'top_nav' => $c['top_nav'],
                            'activity_nav' => $c['activity_nav'],
                            'type' => $type,
                            'type_name' => $name,
                            'updated_at' => date("Y-m-d H-i-s",time()),
                            'created_at' => date("Y-m-d H-i-s",time())
                        ];
                        Menus::query()->insertGetId($c_data);
                    }
                }
            }
        } else {
            Menus::query()->where('type_name', $name)->delete();
        }
    }


    /**
     * 写入/删除 插件/模块
     * @param  int $type 类型 1模块 2插件
     * @param  string $name 唯一名称
     * @param  bool $status 写入、删除
     */
    public function configTable($type, $name, $status){
        if (!$status) {
            if ($type == 1) {
                Modules::query()->where('name', $name)->delete();
            } else {
                Plugins::query()->where('name', $name)->delete();
            }
            return;
        }
        $plugin_dir = app_path() . '/Http/Controllers';
        if ($type == 1) {
            $info_file = $plugin_dir . '/modules/'.$name.'/install/info.json';
        } else {
            $info_file = $plugin_dir . '/plugins/'.$name.'/install/info.json';
        }
        $app_info = $this->getInfoByFile($info_file);
        if ($type == 1) {
            Modules::query()->insert(['name'=>$app_info['name'], 'title'=>$app_info['title'], 'config'=>$app_info['config'], 'updated_at'=>date("Y-m-d H-i-s",time()), 'created_at'=>date("Y-m-d H-i-s",time())]);
        } else {
            Plugins::query()->insert(['name'=>$app_info['name'], 'title'=>$app_info['title'], 'config'=>$app_info['config'], 'updated_at'=>date("Y-m-d H-i-s",time()), 'created_at'=>date("Y-m-d H-i-s",time())]);
        }
    }



    /**
     * 删除文件夹
     * @param string $dirname 目录
     * @return boolean
     */
    public function deldir($dirname)
    {
        if (!$handle = @opendir($dirname)) {
            return false;
        }
        while (false !== ($file = readdir($handle))) {
            if ($file !== "." && $file !== "..") {       //排除当前目录与父级目录
                $file = $dirname . '/' . $file;
                if (is_dir($file)) {
                    self::deldir($file);
                } else {
                    @unlink($file);
                }
            }

        }
        @rmdir($dirname);
        return true;
    }


    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest 目标文件夹
     */
    public function copydirs($source, $dest){
        if (!file_exists($dest)) mkdir($dest);
        $handle = opendir($source);
        while (($item = readdir($handle)) !== false) {
            if ($item == '.' || $item == '..') continue;
            $_source = $source . '/' . $item;
            $_dest = $dest . '/' . $item;
            if (is_file($_source)) copy($_source, $_dest);
            if (is_dir($_source)) self::copydirs($_source, $_dest);
        }
        closedir($handle);
        return true;
    }
}
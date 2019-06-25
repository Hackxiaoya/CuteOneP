<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/15
 * Time: 2:50
 */
namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\ExtensionService;
use App\Models\Plugins;

class PluginsService extends Controller
{



    /*
     * 检索插件列表
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     */
    public static function getPluginsList()
    {
        $plugin_dir = app_path() . '/Http/Controllers/Plugins/';
        $dirs = array_map('basename', glob($plugin_dir.'*', GLOB_ONLYDIR));
        if (!file_exists($plugin_dir)) {
            return ['code'=>1, 'msg'=>'插件目录不可读或者不存在', 'data'=>''];
        }
        $plugins = [];
        $list = Plugins::query()->whereIn('name', $dirs)->get();
        foreach ($list as $plugin) {
            $plugins[$plugin['name']] = $plugin->toArray();
        }

        $extensionObj = new ExtensionService;
        foreach ($dirs as $value) {
            $info_file = $plugin_dir.$value.'/install/info.json';
            $info = $extensionObj->getInfoByFile($info_file);
            $info_flag = $extensionObj->checkInfoFile($info_file);
            if (!$info || !$info_flag) {
                return ['code'=>1, 'msg'=>'插件'.$value.'的信息缺失！', 'data'=>''];
            }
            if (!isset($plugins[$value])) {
                $status = 0; // 未安装
            }else{
                $status = 1; // 已安装
            }

            $plugins[$value] = $info;
            $plugins[$value]['status'] = $status;
        }
        foreach ($plugins as &$val) {
            switch ($val['status']) {
                case 0:  // 未安装
                    $val['status'] = '<button class="layui-btn layui-btn-normal layui-btn-xs install" data-title="'.$val['title'].'" data-name="'.$val['name'].'">安装插件</button>';
                    break;
                case 1:  // 正常
                    $val['status'] = '<button class="layui-btn layui-btn-danger layui-btn-xs uninstall" data-name="'.$val['name'].'">卸载插件</button>';
                    break;
            }
        }
        return $plugins;
    }


    /*
     * 安装插件
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     */
    public static function install($name){
        $extensionObj = new ExtensionService;
        $install_check = $extensionObj->installCheck(2, $name);
        if ($install_check['code'] == 1) {
            return ['code'=>1, 'msg'=>$install_check['msg']];
        }
        $result = $extensionObj->resourceMove(2, $name, true);
        if ($result['code'] == 1) {
            return ['code'=>1, 'msg'=>$result['msg']];
        }
        $extensionObj->menus(2, $name, true);
        $extensionObj->configTable(2, $name, true);
        return $result;
    }


    /*
     * 卸载插件
     * @date: 2019-6-15
     * @author: yyyvy <76836785@qq.com>
     */
    public static function uninstall($name){
        $extensionObj = new ExtensionService;
        $extensionObj->menus(2, $name, false);
        $extensionObj->configTable(2, $name, false);
        $result = $extensionObj->resourceMove(2, $name, false);
        if ($result['code'] == 1) {
            return ['code'=>1, 'msg'=>$result['msg']];
        }
        return $result;
    }


}
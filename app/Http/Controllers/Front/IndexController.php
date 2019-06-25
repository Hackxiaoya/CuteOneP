<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/14
 * Time: 15:59
 */
namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Menus;

use App\Http\Controllers\Front\DiskController;

class IndexController extends Controller
{




    /*
     * 首页
     * @date: 2019-6-14
     * @author: yyyvy <76836785@qq.com>
     */
    public function index(Request $request){
        $index_info = Menus::query()->where([['activate', 1], ['position', 0]])->first()->toArray();
        if (!$index_info) {
            abort(500, '您没设置默认首页！');
        }
        $DiskController = new DiskController();
        return $DiskController->index($request, $index_info['type_name']);
    }
}
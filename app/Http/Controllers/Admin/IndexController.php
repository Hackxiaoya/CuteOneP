<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class IndexController extends BaseController
{

    /*
     * 后台首页
     * @date: 2019-6-8
     * @author: yyyvy <76836785@qq.com>
     */
    public function index(Request $request){
        //dd($request->session());
        return view('admin.index.index', ['top_nav'=>'index', 'activity_nav'=>'index']);
    }
}

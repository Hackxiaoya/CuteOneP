<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/15
 * Time: 12:08
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Menus;

class BaseController extends Controller
{
    public function __construct()
    {
        $menu = Menus::query()->where('position', 1)->get()->toArray();
        View()->share('top_nav');
        View()->share('activity_nav');

    }

}
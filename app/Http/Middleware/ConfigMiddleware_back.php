<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/13
 * Time: 23:41
 */
namespace App\Http\Middleware;

use Closure;
use App\Models\Config;
use App\Models\Disks;

class ConfigMiddleware
{


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $path = $request->getPathInfo();
        $path = explode('/', $path);
        // 闭站拦截，不拦截后台
        if ($path[1] == 'admin') {
            //当 auth 中间件判定某个用户未认证，会返回一个 JSON 401 响应，或者，如果不是 Ajax 请求的话，将用户重定向到 login 命名路由（也就是登录页面）。
            if (!$request->session()->has('username')) {
                return redirect()->guest('admin/login');
            }
        } else {
            $Config =  new Config();
            $config_info = $Config->webConfig();
            if ($config_info['toggle_web_site'] == '0') {
                // 闭站拦截
                return response()->view('error.webClosed');
            }
        }

        // 破解拦截器
        $disk_free = Disks::query()->get()->toArray();
        if (count($disk_free) > 1) {
            return response()->view('error.webClosed');
        }
        return $next($request);
    }

}
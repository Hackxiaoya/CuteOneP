<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/13
 * Time: 23:43
 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Config;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:config')->except('logout');
    }

    public function showLoginForm()
    {
        return view('admin.login.login');
    }



    /**
     * 登陆
     * @Author: yyyvy <76836785@qq.com>
     * @Description:
     * @Time: 2019-6-13
     */
    public function login(Request $request){
        $username = $request->input('username', '');
        $password = $request->input('password', '');
        if(!$username) return response()->json(['code' => 1, 'msg' => '账号不能为空！']);
        if(!$password) return response()->json(['code' => 1, 'msg' => '密码不能为空！']);
        $result_user = Config::where('name', 'username')->first();
        if ($result_user->value != $username){
            return response()->json(['code' => 1, 'msg' => '账号不存在！']);
        }
        $result_pass = Config::where('name', 'password')->first();
        if (!Hash::check($password, $result_pass->value)){
            return response()->json(['code' => 1, 'msg' => '密码错误！']);
        }
        $request->session()->put('username', $username);
        return response()->json(['code' => 0, 'msg' => '登陆成功！']);
    }

    /**
     * 登出
     * @Author: yyyvy <76836785@qq.com>
     * @Description:
     * @Time: 2019-6-13
     */
    public function logout(Request $request){
        //判断session里面是否有值(用户是否登陆)
        if($request->session()->has('username')){
            //移除session
            $request->session()->pull('username',session('username'));
        }
        return redirect()->guest('admin/login');
    }
}
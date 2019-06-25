<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Config;
use App\Models\Menus;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $Config = new Config();
        $webConfig = $Config->webConfig();

        $Menus = Menus::query()->where([['position', 0], ['status', 1]])->orderBy('sort', 'ASC')->get()->toArray();
        // 视图数据共享
        view()->share('webConfig', $webConfig);
        view()->share('menus', $Menus);
    }
}

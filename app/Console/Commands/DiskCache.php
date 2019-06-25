<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DiskCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:DiskCache {method} {disk_id} {types}';
    // php artisan command:DiskCache 方法名 --func=参数
    // 例：php artisan command:DiskCache cache 网盘id 更新类型
    //    php artisan command:DiskCache cache 1 1

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 入口方法
        $method = $this->argument('method');    // 方法名
        $disk_id = $this->argument('disk_id');  // 网盘id
        $types = $this->argument('types');  // 更新类型 1是普通更新，2是增量更新
        //本类中是否存在传来的方法
        if(!method_exists(new self,$method)){
            echo '不存在的方法，请确认输入是否有误！';
        }else{
            self::$method($disk_id, $types);
        }
    }

    public static function cache($disk_id, $types){
        $exitCode = refreshCache($disk_id, $types, '');
        #echo $exitCode;
        #return $exitCode;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Config extends Authenticatable
{


    /**
     * 获取网站配置信息
     * @Author: yyyvy <76836785@qq.com>
     * @Description:
     * @Time: 2019-6-13
     */
    public function webConfig()
    {
        $result = $this->get()->toArray();
        $json_data = array();
        foreach ($result as $v){
            $json_data[$v['name']] = $v['value'];
        }
        unset($json_data['username'], $json_data['password']);
        return $json_data;
    }
}


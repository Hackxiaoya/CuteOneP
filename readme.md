# CuteOneP

## 关于CuteOneP
CuteOneP是CuteOne的PHP版本，沿用一致的UI风格，保持代码精简 框架可扩展;

## 需知
更新缓存是单线程的，因为mysql的瓶颈原因，只能单线程，何况PHP也只有单线程，所以文件较多的，会比较耗时;


## 安装需求
* PHP >= 7.1.3
* OpenSSL PHP 拓展
* PDO PHP 拓展
* Mbstring PHP 拓展
* Tokenizer PHP 拓展
* XML PHP 拓展
* Ctype PHP 拓展
* JSON PHP 拓展
* BCMath PHP 拓展

## 安装说明
* 安装依赖文件
> composer install  

* 虚拟主机用户可以选择下载完整包
> https://github.com/Hackxiaoya/CuteOneP/releases

* 虚拟主机用户
> 需开启伪静态，根目录创建一个.htaccess文件，内容如下：  
```
<IfModule mod_rewrite.c> 
    RewriteEngine on 
    RewriteCond %{REQUEST_URI} !^public 
    RewriteRule ^(.*)$ public/$1 [L] 
</IfModule> 
```
* VPS or 服务器
> 把运行目录设置为public即可

* 修改根目录.env里的数据库连接
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1	数据库地址
DB_PORT=3306	数据库端口
DB_DATABASE=cuteonep	数据库名
DB_USERNAME=root	数据库账号
DB_PASSWORD=root	数据库密码
```

* 访问
> 后台地址是：域名/admin/login  
  默认账号密码 admin  
  默认数据库文件是根目录下的install.sql  直接导入到数据库里，临时先这样；  
  然后还是先添加网盘，更新缓存，设置首页。  
  更新缓存点一下就好了，然后刷新页面，如果有缓存量就是正常更新；  
  你文件很多，你就多等一会；  



## 版本日志
* 1.0.0
> 初始版本;
* 1.1.0
> 修正虚拟空间目录错误;  
> 修正默认挂载错误;  
> 修正默认前端错误;  
> 修正后台菜单添加/显示错误;  
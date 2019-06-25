<!DOCTYPE html>
<html>
<head>
    <title>后台管理</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link href="favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link rel="stylesheet" href="/css/font-awesome.min.css" media="all">
    <link rel="stylesheet" href="/layui/rc/css/layui.css" media="all">
    <link rel="stylesheet" href="/css/admin.css" media="all">
    <!-- 预留空间 给继承的CSS -->
    @yield('style')
</head>
<body>
<div id="LAY_app" class="layadmin-tabspage-none">
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layadmin-flexible">
                    <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                        <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs">
                    <a href="/" target="_blank" title="前台">
                        <i class="layui-icon layui-icon-website"></i>
                    </a>
                </li>
            </ul>
            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item">
                    <a href="javascript:;" layadmin-event="warning">
                        <i class="layui-icon layui-icon-notice"></i>
                        <span class="layui-badge-dot"></span>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;">
                        <cite>{{ Session::get('username') }}</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd layadmin-event="logout" style="text-align: center;">
                            <a href="/admin/logout">注销</a>
                        </dd>
                    </dl>
                </li>
            </ul>
        </div>

        <!-- 侧边菜单 -->
        <div class="layui-side layui-side-menu">
            <div class="layui-side-scroll">
                <div class="layui-logo" lay-href="">
                    <span>管理面板</span>
                </div>
                <ul class="layui-nav layui-nav-tree" lay-shrink="all" lay-filter="layadmin-system-side-menu">
                    <li class="layui-nav-item">
                        <a href="/admin/index" lay-tips="主页">
                            <i class="layui-icon layui-icon-home"></i>
                            <cite>主页</cite>
                        </a>
                    </li>
                    <li class="layui-nav-item @if ($top_nav == 'menus') layui-nav-itemed @endif">
                        <a href="javascript:;" lay-tips="菜单">
                            <i class="fa fa-navicon"></i>
                            <cite>菜单管理</cite>
                            <span class="layui-nav-more"></span>
                        </a>
                        <dl class="layui-nav-child">
                            <dd class="@if ($activity_nav == 'menus_in_list') layui-this @endif">
                                <a href="/admin/menu/0">前台菜单</a>
                            </dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item @if ($top_nav == 'disk') layui-nav-itemed @endif">
                        <a href="javascript:;" lay-tips="网盘">
                            <i class="fa fa-list"></i>
                            <cite>网盘管理</cite>
                            <span class="layui-nav-more"></span>
                        </a>
                        <dl class="layui-nav-child">
                            <dd class="@if ($activity_nav == 'disk_list') layui-this @endif">
                                <a href="/admin/disk">网盘列表</a>
                            </dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item @if ($top_nav == 'author') layui-nav-itemed @endif">
                        <a href="javascript:;" lay-tips="权限">
                            <i class="fa fa-unlock-alt"></i>
                            <cite>权限管理</cite>
                            <span class="layui-nav-more"></span>
                        </a>
                        <dl class="layui-nav-child">
                            <dd class="@if ($activity_nav == 'index') layui-this @endif">
                                <a href="/admin/author/index">权限列表</a>
                            </dd>
                        </dl>
                    </li>
                    <li class="layui-nav-item @if ($top_nav == 'system') layui-nav-itemed @endif">
                        <a href="javascript:;" lay-tips="设置">
                            <i class="layui-icon layui-icon-set"></i>
                            <cite>设置</cite>
                            <span class="layui-nav-more"></span>
                        </a>
                        <dl class="layui-nav-child">
                            <dd class="@if ($activity_nav == 'system_panel') layui-this @endif">
                                <a href="/admin/system/panel">系统概括</a>
                            </dd>
                            <dd class="@if ($activity_nav == 'system_setting') layui-this @endif">
                                <a href="/admin/system/setting">系统设置</a>
                            </dd>
                            <dd class="@if ($activity_nav == 'system_front') layui-this @endif">
                                <a href="/admin/system/front">前端设置</a>
                            </dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>


        <!-- 主体内容 -->
        <div class="layui-body" id="LAY_app_body">
            <div class="layadmin-tabsbody-item layui-show">
                <!-- 预留空间 给继承的html -->
                @yield('content')
            </div>
        </div>
    </div>
</div>

<script type="application/javascript" src="/layui/layui.js"></script>
<script>
    //JavaScript代码区域
    layui.use(['jquery', 'element', 'layer'], function(){
        var $ = layui.$; //重点处
        var element = layui.element;

        // 点击事件列表
        clickMethod = {
            // 刷新
            restart: function() {
                $.ajax({
                    url: "/admin/system/restart"
                    ,type: "POST"
                    ,dataType: "json"
                    ,success: function (data) {
                        if(data.code==0){
                            layer.msg('成功！', {icon: 1});
                            setTimeout(function () {
                                location.reload();
                            },2000)
                        }
                    }
                });
            },
            // 收展左侧菜单
            flexible: function () {
                if($('#LAY_app').hasClass('layadmin-side-shrink')){
                    $('#LAY_app').removeClass('layadmin-side-shrink')
                }else{
                    $('#LAY_app').addClass('layadmin-side-shrink')
                }
            },
            // 提示
            warning: function (e, msg) {
                layer.open({
                    type: 0
                    ,title: '提示'
                    ,icon: 7
                    ,closeBtn: 0
                    ,shade: 0.6
                    ,content: '预留弹框！'
                    ,btn: ['关闭']
                    ,yes: function(index){
                        layer.close(index);
                    }
                });
            }
        };
        // 点击事件绑定
        $('body').on("click", "*[layadmin-event]", function() {
            var _this = $(this);
            clickMethod[_this.attr("layadmin-event")](this);
        });
        // 提示层事件绑定
        $('body').on("mouseenter", "*[lay-tips]", function() {
            var _this = $(this);
            var tips = _this.attr('lay-tips')
            layer.tips(tips, _this);
        }).on("mouseleave", "*[lay-tips]", function() {
            layer.close(layer.tips())
        });

    });
</script>

<!-- 预留空间 给继承的script -->
@yield('script')
</body>
</html>
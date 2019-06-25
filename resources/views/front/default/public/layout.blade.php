<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no" />
    <title>{{ $webConfig['web_site_title'] }} - CuteOne网盘系统</title>
    <meta name="keywords" content="{{ $webConfig['web_site_keyword'] }}">
    <meta name="description" content="{{ $webConfig['web_site_description'] }}">
    {{--<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />--}}
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link rel="stylesheet" href="/themes/default/css/mdui.css">
    <link rel="stylesheet" href="/layui/css/layui.css">
    <link rel="stylesheet" href="/css/lightbox.css">
    <link rel="stylesheet" href="/css/APlayer.min.css">
    <link rel="stylesheet" href="/themes/default/css/style.css">
    <style>
        body {
            background-image: url("{{ $webConfig['web_site_background'] }}") !important;
        }
    </style>
    <!-- 预留空间 给继承的CSS -->
    @yield('style')
    <script type="application/javascript" src="/js/jquery.min.js"></script>
    <script type="application/javascript" src="/js/lightbox.js"></script>
    <script type="application/javascript" src="/layui/layui.js"></script>
    <script type="application/javascript" src="/js/clipboard.min.js"></script>
    <script type="application/javascript" src="/js/APlayer.min.js"></script>
    <script type="application/javascript" src="/js/APEFN.js"></script>
    <script src="/themes/default/js/mdui.min.js"></script>
    <script type="application/javascript" src="/js/clipboard.min.js"></script>
</head>

<body class="mdui-theme-primary-blue-grey mdui-theme-accent-blue">
<div class="mdui-container">
    <div class="mdui-container-fluid header-top">
        <a href="/" class="logo">
            <img src="{{ $webConfig['web_site_logo'] }}" alt="{{ $webConfig['web_site_title']}}">
        </a>
    </div>
    <div class="mdui-container-fluid">
        <div class="mdui-drive nexmoe-item">
            <ul>
                <li>
                    <a href="/">
                        首页
                    </a>
                </li>
                @foreach ($menus as $v)
                    <li>
                        <a href="{{ $v['url'] }}" @if ( $v['target'] == 1) target="_blank" @endif>
                            {{ $v['title'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="zySearch">
            <input id="searchInput" class="search-input" type="text" placeholder="搜索小二丫？" onkeydown="if(event.keyCode==13){search();}">
            <b class="search-img"></b>
            <button class="search-btn btn" onclick="search()">搜索</button>
        </div>
    </div>

    <!-- 预留空间 给继承的html -->
    @yield('content')

    <div class="web_site_copyright">
        {!! $webConfig['web_site_copyright'] !!}
        <span>
            {!! $webConfig['web_site_icp'] !!}
        </span>
    </div>

    <a href="javascript:thumb();" class="mdui-fab mdui-fab-fixed mdui-ripple mdui-color-theme-accent"><i class="mdui-icon material-icons">format_list_bulleted</i></a>
</div>
<div id="aplayer"></div>

<script>
    function thumb(){
        if($('.mdui-fab i').text() == "apps"){
            $('.mdui-fab i').text("format_list_bulleted");
            $('.nexmoe-item').removeClass('thumb');
            $('.nexmoe-item .mdui-icon').css('display','inline-block');
            // $('.nexmoe-item .mdui-icon').show();
            $('.nexmoe-item .mdui-list-item').css("background","");
        }else{
            $('.mdui-fab i').text("apps");
            $('.nexmoe-item').addClass('thumb');
            $('.mdui-col-xs-12 i.mdui-icon').each(function(){
                if($(this).text() == "image" || $(this).text() == "ondemand_video"){
                    var href = $(this).parent().parent().children(0).attr('_src');
                    $(this).hide();
                    $(this).parent().parent().parent().css({"background":"url("+href+") no-repeat center top / cover "});
                }
            });
        }

    }
    $(function(){
        //获取url中的参数
        function getUrlParam(name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
            var r = window.location.search.substr(1).match(reg);  //匹配目标参数
            if (r != null) return unescape(r[2]); return null; //返回参数值
        }
        //修改url中的参数
        function replaceParamVal(url, paramName,replaceWith) {
            var oUrl = decodeURIComponent(url.toString());
            var re = eval('/('+ paramName+'=)([^&]*)/gi');
            var nUrl = oUrl.replace(re,paramName+'='+replaceWith);
            return nUrl;
        }

        $('.icon-sort').each(function (v,k) {
            var sort_table = $(this).attr("data-table");
            var sort_sort = $(this).attr("data-sort");
            if("{{ $sortTable }}" == sort_table){
                // console.log(sort_sort)
                if("{{ $sortType }}" == sort_sort){
                    $(this).attr("data-sort", "less").text("expand_" + "less");
                }
            }
        });
        $('.icon-sort').on('click', function () {
            let sort_table = $(this).attr("data-table");
            let sort_sort = $(this).attr("data-sort");
            let sort_sort_to = (sort_sort == "more") ? "more" : "less";
            $(this).attr("data-sort", sort_sort_to).text("expand_" + sort_sort_to);
            let urlsortTable = getUrlParam("sortTable");
            if(urlsortTable){
                reptable = replaceParamVal(window.location.search, "sortTable", sort_table);
                repsort = replaceParamVal(reptable, "sortType", sort_sort);
                window.location.href = '//' + window.location.host + "/disk/{{ $disk_id }}/" + repsort;
            }else{
                console.log(window.location.href);
                //console.log(reptable);
                //return;
                window.location.href = window.location.href + "&sortTable="+sort_table+"&sortType="+sort_sort
            }
        });

        // 翻页
        $(".page-box .page-click").on('click', function () {
            let page = $(this).attr("data-page");
            let urlpage = getUrlParam("page");
            if(urlpage){
                rep= replaceParamVal(window.location.search, "page", page);
                window.location.href = '//' + window.location.host + "/disk/{{ $disk_id }}/" + rep;
            }else{
                window.location.href = window.location.href + "&page="+page
            }
        });

        // 弹出下载
        $(".down_file").on("click", function () {
            event.stopPropagation();    //  阻止事件冒泡
            var disk = $(this).data("disk"),
                id = $(this).data("id"),
                name = $(this).data("name");
            var url = "/disk/down_file/" + disk + "/" + id + "/?filename=" + name;
            layer.confirm('是否确认下载？', {
                btn: ['确认','取消']
            }, function(index){
                window.location.href = url;
                layer.close(index);
            });
        });

        // word文件打开
        $(".open_word").on("click", function () {
            event.stopPropagation();    //  阻止事件冒泡
            var disk = $(this).data("disk"),
                id = $(this).data("id");
            var url = "/disk/download_info/" + disk + "/" + id;
            $.ajax({
                url : url,
                dataType: 'json',
                success: function(data){
                    window.open("https://view.officeapps.live.com/op/view.aspx?src="+encodeURIComponent(data.data.downloadUrl), "_blank");
                }
            });
        });

        // 外链按钮
        $(".link_file").on("click", function () {
            event.stopPropagation();    //  阻止事件冒泡
            var disk = $(this).data("disk"),
                id = $(this).data("id"),
                name = $(this).data("name");
            var url = "//" + window.location.host + "/disk/down_file/" + disk + "/" + id + "/?filename=" + name;
            // 复制到剪切板
            clipboard = new ClipboardJS(this, {
                text: function(el) {
                    return url;
                }
            });
            clipboard.on('success', function(e) {
                layer.msg("已复制外链地址！");
                clipboard.destroy();
            });
        });

        // 搜索按钮
        search = function () {
            var searchInput = encodeURIComponent($("#searchInput").val());
            let urlsearch = getUrlParam("search");
            if(searchInput) {
                if(urlsearch){
                    searchurl = replaceParamVal(window.location.search, "search", searchInput);
                    window.location.href = "//" + window.location.host + "/disk/{{ $disk_id }}/" + searchurl;
                }else{
                    window.location.href = "//" + window.location.host + "/disk/{{ $disk_id }}/?search=" + searchInput;
                }
            }else{
                layer.msg("No Search!")
            }
        };



        @if ( $webConfig['is_music'] == 1)
            //Music Player
            music = new music({
                container: document.getElementById('aplayer'),
                fixed: true,
                autoplay: true,
                theme: '#FADFA3',
                loop: 'all',
                order: 'list',
                preload: 'metadata',
                volume: 0.7,
                mutex: true,
                listFolded: false,
                listMaxHeight: 90,
            });
            $(".addMusicList").on("click",function () {
                var name = $(this).data("name"),
                    disk = $(this).data("disk"),
                    id = $(this).data("id");
                name = name.replace(".mp3","");
                name = name.replace(".flac","");
                var url = "/disk/down_file/" + disk + "/" + id;
                music.addMusic([{
                    name: name,
                    artist: "未知",
                    url: url,
                    theme: "#ebd0c2"
                }]);
            });
        @endif

    });

    // 弹出视频
    layui.use(['layer'], function() {
        var $ = layui.$; //重点处
        $(".video_open").on("click", function () {
            var disk = $(this).data("disk"),
                id = $(this).data("id");
            var url = "/disk/pop_video/" + disk + "/" + id;
            //适配手机端
            if($(window).width() < 500) {
                var d_width = "95%";
                var shadeClose = true;
                var closeBtn = 0;
            }else{
                var d_width = "50%";
                var shadeClose = false;
                var closeBtn = 1;
            }

            layer.open({
                type: 2,
                title: false,
                area: [d_width, '60%'],
                shade: 0.8,
                closeBtn: closeBtn,
                shadeClose: shadeClose,
                content: url
            });
        });
    });

</script>

<!-- 预留空间 给继承的script -->
@yield('script')
</body>

</html>
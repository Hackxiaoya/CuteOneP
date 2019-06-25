@extends('front.default.public.layout')

@section('style')
    <style>
        .member_tip {
            text-align: center;
            margin: 25px 0 50px;
            color: #f04344;
            font-size: 16px;
            font-weight: bolder;
        }
        .member_tip a {
            padding: 10px 15px;
            color: white;
            background: #f04344;
            text-decoration: none;
            border-radius: 5px;
        }
        .password-box {
            text-align: center;
            padding-bottom: 50px;
        }
        .password-input {
            font-size: 16px;
            border: 2px solid #cecece;
            padding: 10px;
            border-radius: 5px;
        }
        .password-box a {
            margin-left: 10px;
            padding: 10px 15px;
            color: white;
            background: #448aff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
@endsection


@section('content')
    <div class="mdui-container-fluid">
        <div class="mdui-toolbar nexmoe-item">
            <a href="/disk/{{ $disk_id }}">根目录</a>/
            @foreach ($crumbs_list as $v)
                <a href="{{ $crumbs_url }}/{{ urlencode($v['path']) }}">{{ $v['name'] }}</a>/
            @endforeach
        </div>
    </div>
    <div class="mdui-container-fluid">
        <div class="nexmoe-item">
            <div class="mdui-row">
                <div class="member_tip">
                    开通VIP拥有更多权限，无视密码，全站阅读
                    <a href="javascript:void(0);" onclick="openvip()">立即开通</a>
                </div>
                <div class="password-box">
                    <input id="password" class="password-input" type="text" placeholder="请输入查看密码">
                    <a href="javascript:void(0);" id="get_password">确定</a>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script type="application/javascript">
        $("#get_password").on("click", function () {
            var password = $("#password").val();
            $.ajax({
                url: "/disk/approve",
                type: "POST",
                dataType:  'json',
                data: {password:password, disk_id:"{{ $disk_id }}", path:"{{ $path }}", _token:"{{ csrf_token() }}"},
                success:function(e){
                    if(e.code){
                        layer.msg("密码错误");
                    }else{
                        location.reload();
                    }
                }
            })
        });

        function openvip() {
            layer.msg("啊哈，暂时还不支持自助开通，请联系站长")
        }
    </script>
@endsection
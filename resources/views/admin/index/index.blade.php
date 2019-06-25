@extends('admin.public.layout')

@section('style')
    <style>
        .index {
            background: url("/images/index_bg.png") no-repeat;
            background-size: cover;
            width: 100%;
            height: 100%;
        }
        .infocon {
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,.2);
        }
        .infocon h1 {
            color: white;
        }
        .infocon .ad {
            margin-top: 15px;
            color: white;
        }
    </style>
@endsection


@section('content')
    <div class="index">
        <div class="infocon">
            <div style="padding: 5%">
                <h1>
                    当你看到这个页面，证明你使用了CuteOne的PHP版本，CuteOneP
                </h1>
                <div class="ad">
                    这是一个广告<br>
                    如果你没有VPS、不会搭建CuteOne，可租用官方主机；<br>
                    官方提供安装售后，第一时间BUG修正，错误排查修正；<br>
                    你只需要把域名指向过来，然后自己更新资源即可；<br>
                    官方主机速度和demo站一致，demo多快，官方主机多快；<br>
                    详细请加QQ群：8331213  咨询群主；<br>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
@endsection
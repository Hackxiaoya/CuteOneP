@extends('front.default.public.layout')

@section('style')
    <link rel="stylesheet" href="/css/DPlayer.min.css">
    <style>
        .infobox {
            position: relative;
        }
        .infobox h3 {
            margin: 1% 0;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            width: 70%;

        }
        .infobox .info {
            position: absolute;
            top: 5px;
            right: 0;
        }
        .infobox .info a {
            font-size: 14px;
            margin-left: 10px;
            text-decoration: none;
            color: #2b2b2b;
            border: 1px dashed #2b2b2b;
            padding: 2px 5px;
        }
        .infobox .info a:hover {
            color: #565656;
        }
    </style>
@endsection


@section('content')
    <div class="mdui-container-fluid">
        <div class="nexmoe-item">
            <div class="mdui-row">
                <div id="dplayer"></div>

                <div class="infobox">
                    <h3 title="{!! $data['name'] !!}">{!! $data['name'] !!}</h3>
                    <div class="info">
                        <a href="javascript:void(0);" id="copybtn">
                            <i class="fa fa-share"></i>
                            分享
                        </a>
                        <a href="/disk/down_file/{{ $data['disk_id'] }}/{{ $data['file_id'] }}/?filename={!! $data['name'] !!}">
                            <i class="fa fa-download"></i>
                            下载
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script type="application/javascript" src="/js/DPlayer.min.js"></script>
    <script type="application/javascript" src="/js/clipboard.min.js"></script>
    <script type="text/javascript">
        const dp = new DPlayer({
            container: document.getElementById('dplayer'),
            screenshot: true,
            video: {
                url: "{!! $data['downloadUrl'] !!}",
                type: 'auto'
            }
        });

        layui.use(['layer'], function() {
            var $ = layui.$; //重点处
            // 复制到剪切板
            clipboard = new ClipboardJS('#copybtn', {
                text: function(el) {
                    var domain = window.location.href;
                    return domain;
                }
            });
            clipboard.on('success', function(e) {
                layer.msg("已复制分享地址，快发给小伙伴吧！");
                e.clearSelection();
            })
        });
    </script>
@endsection
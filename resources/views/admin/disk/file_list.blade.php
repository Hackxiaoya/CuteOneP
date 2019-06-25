@extends('admin.public.layout')

@section('style')
@endsection


@section('content')
    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-header">
                        文件列表
                    </div>
                    <div class="layui-card-body">
                        <table class="layui-hide" id="list-table" lay-filter="list-table">
                            <thead>
                            <tr>
                                <th lay-data="{field:'name', sort: true}">文件</th>
                                <th lay-data="{field:'size', width:100, sort: true}">大小</th>
                                <th lay-data="{field:'lastModifiedDateTime', width:200, sort: true}">修改时间</th>
                                <th lay-data="{field:'operation', width:160}">操作</th>
                            </tr>
                            </thead>
                            <tbody id="layer-photos">
                            @foreach ($data as $v)
                                <tr>
                                    <td>
                                        @if (array_key_exists('folder', $v))
                                            <a href="{{ $current_url }}/{{ urlencode(str_replace('+', '%2B', $v['name'])) }}">
                                                <i class="fa fa-folder"></i>
                                                {{ $v['name'] }}
                                            </a>
                                        @else
                                            @if ($v['file']['mimeType'] == 'video/mp4')
                                                <a href="javasript:void(0);" onclick="openvideo('{{ $v['thumbnails'][0]['large']['url'] }}');">
                                                    <i class="fa fa-video-camera"></i>
                                                    {{ $v['name'] }}
                                                </a>
                                            @elseif ($v['file']['mimeType'] == 'image/jpeg')
                                                <a href="javasript:void(0);" onclick="openimage('{{ $v['thumbnails'][0]['large']['url'] }}');">
                                                    <i class="fa fa-file-photo-o"></i>
                                                    {{ $v['name'] }}
                                                </a>
                                            @elseif ($v['file']['mimeType'] == 'application/zip')
                                                <a href="javasript:void(0);">
                                                    <i class="fa fa-file-zip-o"></i>
                                                    {{ $v['name'] }}
                                                </a>
                                            @elseif ($v['file']['mimeType'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
                                                <a href="javasript:void(0);">
                                                    <i class="fa fa-file-word-o"></i>
                                                    {{ $v['name'] }}
                                                </a>
                                            @elseif ($v['file']['mimeType'] == 'application/octet-stream')
                                                <a href="javasript:void(0);">
                                                    <i class="fa fa-font"></i>
                                                    {{ $v['name'] }}
                                                </a>
                                            @else
                                                <a href="javasript:void(0);">
                                                    <i class="fa fa-file"></i>
                                                    {{ $v['name'] }}
                                                </a>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        {{ $v['size'] }}
                                    </td>
                                    <td>
                                        {{ $v['lastModifiedDateTime'] }}
                                    </td>
                                    <td>
                                        <a class="layui-btn layui-btn-normal layui-btn-xs" href="javascript:void(0);" onclick="rename_files('{{ $v['id'] }}')">
                                            <i class="fa fa-edit"></i>
                                            重命名
                                        </a>
                                        <a class="layui-btn layui-btn-danger layui-btn-xs" href="javascript:void(0);" onclick="delete_files('{{ $v['id'] }}')">
                                            <i class="fa fa-recycle"></i>
                                            删除
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        layui.use(['layer', 'table'], function(){
            var $ = layui.$;
            var table = layui.table;
            // 静态表格
            table.init('list-table', {
                limit: 30
                ,page: true //开启分页
            });
        });
    </script>
@endsection
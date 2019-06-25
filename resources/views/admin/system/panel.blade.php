@extends('admin.public.layout')

@section('style')
    <style>
        .title {
            color: #009688;
        }
    </style>
@endsection


@section('content')
    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-header">系统概括</div>
                    <div class="layui-card-body" style="overflow: hidden;">
                        <div class="layui-col-md4">
                            <table class="layui-table layui-text">
                                <colgroup>
                                    <col width="100"><col>
                                </colgroup>
                                <tbody>
                                <tr>
                                    <td>系统名称</td>
                                    <td>
                                        <span class="title">CuteOneP - {{ $config['versionType'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>系统版本</td>
                                    <td>
                                        v{{ $config['version'] }}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="layui-col-md4">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
@endsection
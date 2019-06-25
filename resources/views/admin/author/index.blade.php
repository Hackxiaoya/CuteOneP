@extends('admin.public.layout')

@section('style')
@endsection


@section('content')
    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-header">
                        权限列表
                    </div>
                    <div class="layui-card-body">
                        <div style="padding-bottom: 10px;">
                            <button class="layui-btn" layadmin-event="add">添加规则</button>
                        </div>
                        <table class="layui-hide" id="list-table" lay-filter="list-table"></table>
                        <script type="text/html" id="list-table-bar">
                            <a class="layui-btn layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>
                            <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        layui.use(['layer', 'table', 'form'], function(){
            var $ = layui.$;
            var form = layui.form;

            // 新增一个新增设备事件
            clickMethod.add = function () {
                layer.open({
                    type: 2
                    ,title: '添加规则'
                    ,shadeClose: true
                    ,area: ["550px", "400px"]
                    ,content: '../author/edit/0'
                    ,end: function () {
                        tablerender();  // 渲染表格
                    }
                })
            };

            var table = layui.table;
            function tablerender(){
                table.render({
                    elem: '#list-table'
                    ,url: './index'
                    ,limit: 30
                    ,page: true //开启分页
                    ,where: {
                        time: Date.now()
                    }
                    ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                    ,cols: [[
                        {field:'id', width:80, title: 'ID'}
                        ,{field:'title', width:150, title: '标题'}
                        ,{field:'disk_name', width:150, title: '网盘名称'}
                        ,{field:'path', width:'30%', title: '路径'}
                        ,{field:'password', width:100, title: '密码'}
                        ,{field:'updated_at', width:180, title: '更新时间'}
                        ,{field:'created_at', width:180, title: '创建时间'}
                        ,{title:"操作", width:150, align:"center", fixed:"right", toolbar:"#list-table-bar"}
                    ]]
                });
            };
            tablerender();  // 渲染表格
            table.on('tool(list-table)', function(obj){
                var data = obj.data; //获得当前行数据
                var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
                var tr = obj.tr; //获得当前行 tr 的DOM对象

                if(layEvent === 'del'){ //删除
                    layer.confirm('真的删除这个规则吗？', function(index){
                        //向服务端发送删除指令
                        $.ajax({
                            url: "../author/del/"+data.id
                            ,type: "GET"
                            ,dataType: "json"
                            ,success: function (data) {
                                if(data.code==0){
                                    obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                    layer.msg('删除成功！', {icon: 1});
                                    tablerender();
                                }else{
                                    layer.msg(data.msg)
                                }
                            }
                        });
                    });
                } else if(layEvent === 'edit'){ //编辑
                    layer.open({
                        type: 2
                        ,title: '编辑规则'
                        ,shadeClose: true
                        ,area: ["550px", "550px"]
                        ,content: '../author/edit/'+data.id
                        ,end: function () { // 层被关闭，重新渲染
                            tablerender();
                        }
                    });
                }
            });
        });
    </script>
@endsection
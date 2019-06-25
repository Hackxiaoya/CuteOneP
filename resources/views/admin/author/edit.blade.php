<link rel="stylesheet" href="/css/font-awesome.min.css" media="all">
<link rel="stylesheet" href="/layui/rc/css/layui.css" media="all">
<link rel="stylesheet" href="/css/admin.css" media="all">

<div class="layui-form" style="padding: 20px 30px 0 0;">
    <form id="form1" onsubmit="return false" action="##" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="id" value="{{ $data['id'] }}">
        <div class="layui-form-item">
            <label class="layui-form-label">标题</label>
            <div class="layui-input-block">
                <input type="text" name="title" value="{{ $data['title'] }}" placeholder="请输入标题" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">网盘</label>
            <div class="layui-input-block">
                <select name="disk_id" placeholder="请选择网盘" lay-filter="disk_id">
                    @foreach ($disk_list as $d)
                        <option value="{{ $d['id'] }}" @if ($d['id'] == $data['disk_id']) selected @endif>{{ $d['title'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">文件夹</label>
            <div class="layui-input-block">
                <input type="text" name="path" value="{{ $data['path'] }}" placeholder="请输入文件夹路径,例：/abc/ddd" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">密码</label>
            <div class="layui-input-block">
                <input type="text" name="password" value="{{ $data['password'] }}" placeholder="请输入密码" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">显示方式</label>
            <div class="layui-input-block">
                <input type="radio" name="login_hide" value="1" title="全显示" @if ($data['login_hide'] == 1)checked @endif>
                {{--<input type="radio" name="login_hide" value="2" title="登陆可见" @if ($data['login_hide'] == 2)checked @endif>--}}
                {{--<input type="radio" name="login_hide" value="3" title="隐藏局部文字" @if ($data['login_hide'] == 3)checked @endif>--}}
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-inline">
                <button id="form-submit" class="layui-btn">保存</button>
            </div>
        </div>
    </form>
</div>

<script type="application/javascript" src="/layui/layui.js"></script>
<script>
    layui.use(['form', 'layer'], function(){
        var $ = layui.$; //重点处
        var form = layui.form;

        // 确认按钮
        $('#form-submit').on('click', function () {
            $.ajax({
                url: "../edit/{{ $data['id'] }}"
                ,type: "POST"
                ,dataType: "json"
                ,data: $('#form1').serialize()
                ,success: function (data) {
                    if(data.code==0){
                        var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                        parent.layer.close(index);
                    }else{
                        layer.msg(data.msg)
                    }
                }
            });
        });
    });
</script>
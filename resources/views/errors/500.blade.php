<!doctype html>
<html style="background: #e4e4e4;">
<head>
    <meta charset="utf-8">
    <title>500错误</title>
    <style>
        a {
            text-decoration: none;
        }
        .notice_page {
            margin-top: 5%;
        }
        h2, h4 {
            text-align: center;
        }
        .notice_page p {
            text-align: center;
            color: #00c091;
        }
        .notice_page p i {
            font-style: normal;
            font-weight: bold;
        }
        .notice_404_p {
            background: url("/images/404_02.png");
            width: 150px;
            height: 200px;
            margin: 0 auto;
        }
        .home_page {
            margin: 0 auto;
            height: 40px;
            width: 300px;
            padding-left: 82px;
        }
        .home_page a {
            display: block;
            padding: 5px;
            color: #676767;
            float: left;
            margin: 0 auto;
            border: 1px solid #ccc;
            border-radius: 3px;
            background: #e8e8e8;
            margin: 20px 15px 0;
        }
    </style>
</head>
<body>
<div class="notice_page">
    <div class="notice_404_p"></div>
    <h4>这是一个500错误</h4>
    <h2>别慌，只是出现了一些错误而已！</h2>
    <p>
        <i>错误提示：</i>
        {{ $exception->getMessage() }}
    </p>
    <div class="home_page">
        <a class="notice_retun" href="javascript:history.go(-1);">返回上一页</a>
        <a class="notice_home" href="/">返回首页</a>
    </div>
</div>

</body>
</html>
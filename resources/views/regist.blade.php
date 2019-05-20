<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Regist</title>
</head>
<body>
    <h3>注册</h3>
    <hr>
    <form action="/registDo" method="post" enctype="multipart/form-data">
        @csrf
        <input type="text" name="username" placeholder="请输入企业名称"><br>
        <input type="text" name="user" placeholder="请输入法人"><br>
        <input type="text" name="code" placeholder="请输入企业工商号码"><br>
        <input type="text" name="card_code" placeholder="请输入税务号"><br>
        <input type="file" name="zhizhao"><br>
        {{--<input type="text" name="username" placeholder="请输入企业名称"><br>--}}
        {{--<input type="text" name="username" placeholder="请输入企业名称"><br>--}}
        {{--<input type="text" name="username" placeholder="请输入企业名称"><br>--}}
        {{--<input type="text" name="username" placeholder="请输入企业名称"><br>--}}
        <input type="submit" value="注册提交">
    </form>
</body>
</html>
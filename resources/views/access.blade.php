<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Access</title>
</head>
<body>
    <h3>恭喜注册成功！</h3>
    <hr>
    <h4>用户id号为{{$id}}的用户注册后获得如下信息：</h4>
    <br>
    <table border="1">
        <tr>
            <td>APPID:</td>
            <td>{{$appid}}</td>
        </tr>
        <tr>
            <td>KEY:</td>
            <td>{{$key}}</td>
        </tr>
    </table>
    <br>
    <button id="btu">点击获取access_token</button>
    当前的access_token:<font color="#00ffff"></font>
    <br>
    <br>
    <button id="g_ip">点击显示客户端ip</button>
    当前客户端的ip:<span style="color: #bcd42a"></span>
    <br>
    <br>
    <button id="g_ua">点击获取客户端的UA</button>
    当前客户端的UA:<p style="color: #bcd42a"></p>
    <br>
    <br>
    <button id="g_info">点击获取当前用户的信息</button>
    <br>
    <div></div>

</body>
<script src="/js/jquery-1.12.4.min.js"></script>
<script>
    $(function(){
        var appid ="{{$appid}}";
        var key ="{{$key}}";
        //获取token
        $('#btu').click(function(){
            $.ajax({
                url:"/get_access",
                data:{appid:appid,key:key},
                dataType:'json',
                method:'get',
                success:function(res){
                    alert(res.msg);
                    if(res.err<5000){
                        $('font').text(res.data.token);
                    }
                }
            })
        })
        //点击显示客户端ip
        $('#g_ip').click(function(){
            $.ajax({
                url:"/get_kip",
                data:{appid:appid,key:key},
                dataType:'json',
                method:'get',
                success:function(res){
                    alert(res.msg);
                    if(res.err<5000){
                        $('span').text(res.data.ip);
                    }
                }
            })
        })
        //点击显示客户端ua
        $('#g_ua').click(function(){
            $.ajax({
                url:"/get_kua",
                data:{appid:appid,key:key},
                dataType:'json',
                method:'get',
                success:function(res){
    //                    console.log(res);
                    alert(res.msg);
                    if(res.err<5000){
                        $('p').text(res.data.ip);
                    }
                }
            })
        })
        //点击显示用户信息
        $('#g_info').click(function(){
            $.ajax({
                url:"/get_userinfo",
                data:{appid:appid,key:key},
                dataType:'json',
                method:'get',
                success:function(res){
                    if(res.err<5000){
                        $('div').html("<table><tr><td>企业名：</td><td>"+res.msg.username+"</td></tr><tr><td>法人：</td><td>"+res.msg.user+"</td></tr><tr><td>税务号：</td><td>"+res.msg.code+"</td></tr><tr><td>执照名：</td><td><img src='/"+res.msg.zhizhao+"'></td></tr><tr><td>工商卡号：</td><td>"+res.msg.card_code+"</td></tr></table>");
                    }else{
                        alert(res.msg);
                    }
                }
            })
        })
    })


</script>
</html>
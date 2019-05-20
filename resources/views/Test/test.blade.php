<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Access</title>
</head>
<body>
	<h3>您的注册正在审核中，请稍等</h3>
</body>
<script src="js/jquery-1.12.4.min.js"></script>
<script>
setInterval("find()",1000);
    function find(){
    $.ajax({
        url:"/get_status/{{$id}}",
        dataType:'json',
        method:'get',
        success:function(res) {
            if (res.err == 1) {
                $('body').text(res.msg);
            } else if (res.err == 3) {
                $('body').text(res.msg);
                location.href = '/regist';
            } else {
                $('body').text(res.msg);
                location.href = '/access_token/{{$id}}';
            }
        }
    })
}
</script>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>网安信息平台</title>
</head>
<body>
<input id="token" type="hidden" value="{{$data->token}}">
<script>
    var token;
    token = document.getElementById('token').value;
    localStorage.setItem("token", token);//token存到本地，每次请求接口携带
    location.href = "www.baidu.com";
</script>
</body>
</html>
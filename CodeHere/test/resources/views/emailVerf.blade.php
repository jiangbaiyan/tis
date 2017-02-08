<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>
<h1>Hi!</h1>
<p>请点击下方链接激活你的账号</p>
<a href="{{ URL('email/users/active')."/".$token."/".$email}}" target="_blank">点击激活您的账号</a>
</body>
</html>
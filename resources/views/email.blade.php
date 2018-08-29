<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <style>
        p{
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
<h2>{{$name}}，您好</h2>
<p>此邮件由通知系统自动发出</p>
<p>点击查看或下载附件: </p>
<p>注：如遇到不能直接点击链接打开的情况，请复制该链接到浏览器地址栏访问即可</p>
@foreach($fileUrls as $fileUrl)
    <p>{{$fileUrl}}</p>
@endforeach
</body>
</html>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <style>
        p{
            color: red;
        }
    </style>
</head>
<body>
<h2>{{$name}}，您好</h2>
<p>此邮件由通知系统自动发出</p>
<p>点击查看或下载附件: </p>
@foreach($fileUrls as $fileUrl)
<p>{{$fileUrl}}</p>
@endforeach
</body>
</html>
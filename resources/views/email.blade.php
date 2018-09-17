<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <style>
        p{
            color: #ff3333;
        }
    </style>
</head>
<body>
<h2>{{$name}}，您好</h2>
<p>此邮件由通知系统自动发出</p>
<p>点击下载附件: </p>
@foreach($fileUrls as $fileUrl)
    <p><a href="{{$fileUrl}}">{{pathinfo(parse_url($fileUrl)['path'])['basename']}}</a></p>
@endforeach
</body>
</html>
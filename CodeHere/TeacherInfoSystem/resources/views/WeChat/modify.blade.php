<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="format-detection" content="telephone=no">
    <title>修改信息</title>
    <style type="text/css">
        * {
            font-family: "微软雅黑";
            font-weight: bold;
        }

        input[type=text] {
            width: 210px;
            height: 20px;
            margin-top: 20px;
            font-size: 17px;
        }

        input[type=submit] {
            width: 160px;
            height: 40px;
            padding: 8px;
            position: relative;
            left: 50%;
            margin-left: -80px;
            margin-top: 40px;
            background-color: #428bca;
            border-color: #357ebd;
            color: #fff;
            -moz-border-radius: 10px;
            -webkit-border-radius: 10px;
            border-radius: 10px; /* future proofing */
            -khtml-border-radius: 10px; /* for old Konqueror browsers */
            text-align: center;
            vertical-align: middle;
            border: 1px solid transparent;
            font-weight: 900;
            font-size: 125%
        }
    </style>
</head>
<body>
@if(isset($student))
    <form action="/modify/{{$student->id}}">
        修改手机：<input type="text" name="phone" value="{{$student->phone}}"><br>
        修改邮箱：<input type="text" name="email" value="{{$student->email}}"><br>
        <input type="hidden" name="type" value="1">
        <input type="submit" value="提交">
    </form>
@elseif(isset($teacher))
    <form action="/modify/{{$teacher->id}}">
        修改邮箱：<input type="text" name="email" value="{{$teacher->email}}"><br>
        <input type="hidden" name="type" value="2">
        <input type="submit" value="提交">
    </form>
@else
    <form action="/modify/{{$graduate->id}}">
        修改手机：<input type="text" name="phone" value="{{$graduate->phone}}"><br>
        修改邮箱：<input type="text" name="email" value="{{$graduate->email}}"><br>
        <input type="hidden" name="type" value="3">
        <input type="submit" value="提交">
    </form>
@endif
<br>
<hr>
</body>
</html>
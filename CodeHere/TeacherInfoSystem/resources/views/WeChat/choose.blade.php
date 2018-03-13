<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no">
    <title>绑定成功</title>
    <style type="text/css">
        p{
            font-size: 20px;
        }
        #success{
            text-align: center;
            font-size: 28px;
            color: red;
            font-weight: bold;
        }
        li{
            font-size: 23px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<p id="success">恭喜您，信息绑定成功！</p>
<hr>
<p>您目前已录入的信息：</p>
@if(isset($student))
<ul>
    <li>学号：{{$student->userid}}</li>
    <li>姓名：{{$student->name}}</li>
    <li>班级：{{$student->class_num}}</li>
    <li>手机号：{{$student->phone}}</li>
    <li>邮箱：{{$student->email}}</li>
    <li>辅导员：
        @if($student->account_id == '41451')
            卞广旭
        @elseif($student->account_id == '40365')
            苏晶
        @else
            冯尉瑾
        @endif    
    </li>
</ul>
@elseif(isset($teacher))
    <ul>
        <li>工号：{{$teacher->userid}}</li>
        <li>姓名：{{$teacher->name}}</li>
        <li>邮箱：{{$teacher->email}}</li>
    </ul>
@else
<ul>
    <li>姓名：{{$graduate->name}}</li>
    <li>学号：{{$graduate->userid}}</li>
    <li>手机号：{{$graduate->phone}}</li>
    <li>邮箱：{{$graduate->phone}}</li>
    <li>辅导员：
        @if($student->account_id == '41978')
            袁理峰
        @endif
    </li>
</ul>
@endif
<hr>
<p>若想修改您已填写的个人信息，请在公众号上留言。备注您的姓名学号，以及要修改的内容。
<p>如有对平台改进的建议和bug也可以提出，我们会及时处理，谢谢！</p>
</body>
</html>
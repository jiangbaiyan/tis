<!DOCTYPE html>
<html lang="cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no">
    <title>请先完善信息</title>
    <style>
        *{
            font-family: "微软雅黑";
            font-size: 20px;
        }
        .submit{
            display: block;
            width: 120px;
            height: 50px;
            margin: 0 auto;
        }
        .bold{
            display: block;
            font-weight: bold;
            color: red;
        }
        div{
            padding: 5px;
            width: 100%;
        }
    </style>
    <script type="text/javascript">

    </script>
</head>
<body>
<form action="https://tis.cloudshm.com/submit" method="post" name="form">
    <div>
        <label for="phone" class="bold">请输入你的联系电话：</label><br><input type="text" name="phone" id="phone">
        <p class="bold">请选择你的辅导员：</p>
        <p><input type="radio" name="teacher" value="卞广旭" checked> 卞广旭</p>
        <p><input type="radio" name="teacher" value="苏晶"> 苏晶</p>
        <p><input type="radio" name="teacher" value="冯尉瑾"> 冯尉瑾</p>
        <p ><input type="submit" class="submit"></p>
    </div>
</form>
</body>
</html>
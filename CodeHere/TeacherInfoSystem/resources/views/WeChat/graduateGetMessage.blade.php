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
            margin: 0;
            padding: 0;
        }
        .submit{
            display: block;
            /*width: 120px;*/
            height: 50px;
            margin: 90px auto 30px auto;
            -webkit-appearance: none;
        }
        .bold{
            display: block;
            font-weight: bolder;
            color: darkorange;
            font-size: 22px;
        }
        .content{
            /*width: 100%;*/
            padding: 10px;
        }
        .footer{
            /*width: 100%;*/
            height: 60px;
            font-size: 12px;
            color: #7d7d7d;
            text-align: center;
            bottom: 5px;
            position: fixed;
            margin-left: 30px;
        }
        .alert{
            font-size: 18px;
            color: red;
            margin: 0 auto;
            text-align: center;
        }
        .submit{
            width: 100px;
            height: 50px;
            position: fixed;
            bottom: 80px;
            left: 50%;
            margin-left: -50px;
        }
        input{
            margin-top: 7px;
        }
    </style>
</head>
<body>
<div class="content">
    <form action="https://tis.cloudshm.com/graduateSubmit" method="post" name="form">
        <label for="phone" class="bold">请输入你的联系电话：</label><br><input type="text" name="phone" id="phone" value="{{old('phone')}}"><br><br>
        <label for="email" class="bold">请输入你的邮箱：</label><br><input type="text" name="email" id="email" value="{{old('email')}}"><br><br>
        <p class="bold">请选择你的辅导员：</p>
        <p><input type="radio" name="teacher" value="袁理锋" checked> 袁理锋</p>
        @if (count($errors) > 0)
            <div class="alert alert-danger bold">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <p ><input type="submit" class="submit" value="提交"></p>
    </form>
</div>
<div class="footer">
    <p>杭州电子科技大学网络空间安全学院</p>
</div>
</body>
</html>
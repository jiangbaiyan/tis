<!DOCTYPE html>
<html class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>网安信息平台</title>
	<style>
		#main {
			width: 310px;
			padding: 15px;
			margin: auto;
		}
		h1 {
			font-size: 25px;
			color: #333;
			font-weight: 400;
			text-align: center;
		}
		.item {
			margin-bottom: 15px;
		}
		span.key {
			border-left: 3px solid #b3d9ff;
			padding-left: 15px;
			width: 80px;
			font-weight: 400;
			color: #666;
		}
		.item:nth-child(odd) span.key {
			border-left-color: #b3c6ff;
		}
		span.val {
			font-weight: 300;
			color: #333;
			letter-spacing: 1.3px;
			overflow:hidden;
			white-space:nowrap;
			text-overflow: ellipsis;
			width: 199px;
		}
		span.key, span.val {
			font-size: 18px;
			display: inline-block;
			vertical-align:top;
		}
		.back {
			border-radius: 5px;
			padding: 12px 16px;
			font-size: 18px;
			text-decoration: none;
			margin: 25px 0 0 90px;
			color: #fff;
			position: relative;
			border: none;
			background-color: #55acee;
		}
	</style>
</head>
<body>
<div id="main">
	<h1>您的信息已绑定成功</h1>
	<br>
	<div class="item">
		<span class="key">姓名</span>
		<span class="val">{{$data->name}}</span>
	</div>
	<div class="item">
		@if (\App\Http\Model\Common\User::getUserType($data->uid) == \App\Http\Model\Common\User::TYPE_TEACHER)
			<span class="key">工号</span>
		@else
			<span class="key">学号</span>
		@endif
		<span class="val">{{$data->uid}}</span>
	</div>
	@if (!empty($data->grade))
		<div class="item">
			<span class="key">年级</span>
			<span class="val">{{$data->grade}}</span>
		</div>
	@endif
	@if (!empty($data->class))
		<div class="item">
			<span class="key">班级</span>
			<span class="val">{{$data->class}}</span>
		</div>
	@endif
	@if (!empty($data->major))
		<div class="item">
			<span class="key">专业</span>
			<span class="val">{{$data->major}}</span>
		</div>
	@endif
	<div class="item">
		<span class="key">手机</span>
		<span class="val">{{$data->phone}}</span>
	</div>
	<div class="item">
		<span class="key">邮箱</span>
		<span class="val" style="letter-spacing: 0.5px">{{$data->email}}</span>
	</div>
	@if (!empty($data->teacher_id))
		<div class="item">
			<span class="key">辅导员</span>
			<span class="val">{{\App\Http\Model\Teacher::$instructorMapping[$data->teacher_id]}}</span>
		</div>
	@endif
	<input id="token" type="hidden" value="{{$data->token}}">
	<button id="back" class="back" onclick="back()">返回首页</button>
</div>
<script>
    var token;
    token = document.getElementById('token').value;
    localStorage.setItem("token", token);//token存到本地，每次请求接口携带

    var returnBtn = document.getElementById('back');

    returnBtn.onclick = function () {
        location.href = "https://tis.hzcloudservice.com/client";
    };
</script>
</body>
</html>
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
		}
		span.key, span.val {
			font-size: 18px;
			display: inline-block;
		}
		.back {
			border-radius: 5px;
			padding: 12px 16px;
			font-size: 18px;
			text-decoration: none;
			margin: 50px auto;
			color: #fff;
			position: relative;
			display: none;
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
			<span class="key">学号/工号</span>
			<span class="val">{{$data->uid}}</span>
		</div>
		<div class="item">
			<span class="key">姓名</span>
			<span class="val">{{$data->name}}</span>
		</div>
		@if (!empty($data->class))
			<div class="item">
				<span class="key">班级</span>
				<span class="val">{{$data->class}}</span>
			</div>
		@endif
		<div class="item">
			<span class="key">手机号</span>
			<span class="val">{{$data->phone}}</span>
		</div>
		<div class="item">
			<span class="key">邮箱</span>
			<span class="val">{{$data->email}}</span>
		</div>
		@if (!empty($data->dean))
			<div class="item">
				<span class="key">辅导员</span>
				<span class="val">{{$data->dean}}</span>
			</div>
		@endif
		<input id="token" type="hidden" value="{{$data->token}}">
		<button id="back" class="back" onclick="back()">返回上一页面</button>
	</div>
	<script>
		var param;
		var token;
		token = document.getElementById('token').value;
        localStorage.setItem("token", token);//token存到本地，每次请求接口携带
		function getParam(url) {
			var search = (typeof url !== 'undefined') 
				? url.slice(url.indexOf('?') + 1)
				: location.search.substring(1);
			if (!search) 
				return false;
			return JSON.parse('{"' + decodeURI(search)
				.replace(/"/g, '\\"')
				.replace(/&/g, '","')
				.replace(/=/g,'":"') + '"}');
		}
		function back() {
			location.href = decodeURI(param.backurl) + (param.id ? '?id=' + param.id : '');
		}
		document.addEventListener("DOMContentLoaded", function(event) { 
			param = getParam();
			if (param && param.backurl) {
				document.getElementById('back').style.display = 'block';
			}
		});
	</script>
</body>
</html>
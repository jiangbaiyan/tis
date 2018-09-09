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
			width: 340px;
			padding: 15px;
			margin: auto;
		}
		h1 {
			margin-top: 80px;
			font-size: 25px;
			color: #333;
			font-weight: 400;
			text-align: center;
		}
		.smt {
			border-radius: 5px;
			padding: 10px 25px;
			font-size: 18px;
			text-decoration: none;
			margin: 50px auto;
			color: #fff;
			position: relative;
			border: none;
			display: block;
			background-color: #55acee;
		}
		label {
		  font-size: 21px;
		  color: #666;
		  width: 110px;
		  margin-right: 10px;
		  position: relative;
		  top: 3px;
		  font-weight: 300;
		  text-align: left;
		  display: inline-block;
		}
		input[type="text"], select {
		  color: #444;
		  border: 1px solid #ddd;
		  padding: 6px 8px;
		  letter-spacing: 1px;
		  font-size: 15px;
		  width: 180px;
		}
		select {
			width: 198px;
		}
        .alert{
            font-size: 18px;
            color: #ff3333;
            margin: 0 auto;
            text-align: center;
			font-weight: 400;
        }
		.alert ul{
			list-style: none;
		}
	</style>
</head>
<body>
	<form id="main" method="GET" action="{{url('/api/v1/login/saveData')}}">
		<h1>请填写您的信息</h1>
		<br>
		<label for="phone">联系电话</label>
		<input type="text" name="phone" value="{{!empty($data) ? $data->phone : old('phone')}}"><br><br>
		<label for="email">电子邮箱</label>
		<input type="text" name="email" value="{{!empty($data) ? $data->email : old('email')}}"><br><br>
		@if ($idType == \App\Http\Model\Common\User::TYPE_STUDENT || $idType == \App\Http\Model\Common\User::TYPE_GRADUATE)
			<label for="instructor">辅导员</label>
			<select type="text" name="instructor">
				<option value="5">卞广旭</option>
				<option value="2">冯尉瑾</option>
				<option value="41">徐诚</option>
				<option value="18">袁理锋</option>
				<option value="42">申延召</option>
			</select>
		@endif
        @if (count($errors) > 0)
            <div class="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
		<input class="smt" type="submit" value="提交信息">
	</form>
</body>
</html>
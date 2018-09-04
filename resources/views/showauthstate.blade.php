<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>网安信息平台教师权限管理系统</title>
    <style>
        form{
            border: 1px solid black;
        }
    </style>
</head>
<body>
@foreach($teacher as $item)
    <form action="{{url('/api/v1/auth/pc/setAuthState')}}" method="post">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        姓名：{{$item->name}}
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        |
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        通知模块权限：
        @if ($item->info_auth_state == \App\Http\Model\Teacher::NORMAL)
            普通教师<input type="radio" name="info_auth_state" value="0" checked>
            辅导员<input type="radio" name="info_auth_state" value="1">
            教务老师<input type="radio" name="info_auth_state" value="2">
        @elseif ($item->info_auth_state == \App\Http\Model\Teacher::INSTRUCTOR)
            普通教师<input type="radio" name="info_auth_state" value="0">
            辅导员<input type="radio" name="info_auth_state" value="1" checked>
            教务老师<input type="radio" name="info_auth_state" value="2">
        @else
            普通教师<input type="radio" name="info_auth_state" value="0">
            辅导员<input type="radio" name="info_auth_state" value="1">
            教务老师<input type="radio" name="info_auth_state" value="2" checked>
        @endif
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        |
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        请假模块权限：
        @if ($item->leave_auth_state == \App\Http\Model\Teacher::NORMAL)
            普通教师<input type="radio" name="leave_auth_state" value="0" checked>
            辅导员<input type="radio" name="leave_auth_state" value="1">
            教务老师<input type="radio" name="leave_auth_state" value="2">
        @elseif ($item->leave_auth_state == \App\Http\Model\Teacher::INSTRUCTOR)
            普通教师<input type="radio" name="leave_auth_state" value="0">
            辅导员<input type="radio" name="leave_auth_state" value="1" checked>
            教务老师<input type="radio" name="leave_auth_state" value="2">
        @else
            普通教师<input type="radio" name="leave_auth_state" value="0">
            辅导员<input type="radio" name="leave_auth_state" value="1">
            教务老师<input type="radio" name="leave_auth_state" value="2" checked>
        @endif
            <input type="hidden" name="uid" value="{{$item->uid}}">
        |        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" value="点击修改权限">
    </form>
    <br>
@endforeach
</body>
</html>
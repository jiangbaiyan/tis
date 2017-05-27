<?php

namespace App\Http\Controllers\Science;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Join_Meeting;
use App\Account;

class JoinMeetingController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        $user = $request->input('user');
        if (!$request->input('meeting_name') || !$request->input('meeting_place') || !$request->input('meeting_time') || !$request->input('is_domestic')) {
            return response()->json(['status' => 400, 'msg' => 'missing parameters']);
        }
        $account = Account::where('user', '=', $user)->first();
        if (!$account) {
            return response()->json(['status' => 404, 'msg' => 'user not exists']);
        }
        $join_Meeting = Join_Meeting::create($data);
        if (!$join_Meeting) {
            return response()->json(['status' => 402, 'msg' => 'join_Meeting created failed']);
        }
        $join_Meeting->name = $account->name;
        $join_Meeting->icon_path = $account->icon_path;
        $join_Meeting->save();
        return response()->json(['status' => 200, 'msg' => 'join_Meeting created successfully']);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $id = $request->input('id');
        $join_Meeting = Join_Meeting::find($id);
        if (!$join_Meeting) {
            return response()->json(['status' => 404, 'msg' => 'join_Meeting not exists']);
        }
        $join_Meeting->update($data);
        if ($join_Meeting->save()) {
            return response()->json(["status" => 200, "msg" => "join_Meeting update successfully"]);
        } else {
            return response()->json(["status" => 402, "msg" => "join_Meeting update failed"]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $join_Meeting = Join_Meeting::find($id);
        if (!$join_Meeting) {
            return response()->json(['status' => 404, 'msg' => 'join_Meeting not exists']);
        }
        if ($join_Meeting->delete()) {
            return response()->json(['status' => 200, 'msg' => 'join_Meeting deleted successfully']);
        } else {
            return response()->json(['status' => 402, 'msg' => 'join_Meeting deleted failed']);
        }
    }

    public function getVerifiedIndex(Request $request)
    {//获取已审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user', '=', $user)->first();
        if (!$account) {
            return response()->json(['status' => 404, 'msg' => 'user not exists']);
        }
        if ($request->input('is_domestic') == '国内'){//如果参加国内会议
            if ($account->science_level) {//如果是超级用户，可以看所有表中的信息
                $join_Meetings = Join_Meeting::select('id', 'user', 'name', 'meeting_name', 'verify_level', 'icon_path', 'updated_at')->where(['verify_level' => 1,'is_domestic' => '国内'])->orderBy('updated_at')->paginate(6);
            }
            else {//如果是普通用户，只能看自己的信息
                $join_Meetings = Join_Meeting::select('id', 'user', 'name', 'meeting_name', 'verify_level', 'icon_path', 'updated_at')->where(['user' => $user, 'verify_level' => 1,'is_domestic' => '国内'])->orderBy('updated_at')->paginate(6);
                if (!$join_Meetings) {
                    return response()->json(['status' => 402, 'msg' => 'join_Meetings required failed']);
                }
            }
        }
        else{//如果参加国外会议
            if ($account->science_level) {//如果是超级用户，可以看所有表中的信息
                $join_Meetings = Join_Meeting::select('id', 'user', 'name', 'meeting_name', 'verify_level', 'icon_path', 'updated_at')->where(['verify_level' => 0,'is_domestic' => '国内'])->orderBy('updated_at')->paginate(6);
            }
            else {//如果是普通用户，只能看自己的信息
                $join_Meetings = Join_Meeting::select('id', 'user', 'name', 'meeting_name', 'verify_level', 'icon_path', 'updated_at')-where(['verify_level' => 0,'is_domestic' => '国内'])->orderBy('updated_at')->paginate(6);
                if (!$join_Meetings) {
                    return response()->json(['status' => 402, 'msg' => 'join_Meetings required failed']);
                }
            }
        }
        return response()->json(['status' => 200, 'msg' => 'join_Meetings required successfully', 'name' => $account->name, 'icon_path' => $account->icon_path, 'science_level' => $account->science_level, 'data' => $join_Meetings]);
    }


    public function getNotVerifiedIndex(Request $request)
    {//获取未审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user', '=', $user)->first();
        if (!$account) {
            return response()->json(['status' => 404, 'msg' => 'user not exists']);
        }
        if ($account->science_level) {//如果是超级用户，可以看所有表中的信息
            $join_Meetings = Join_Meeting::select('id', 'user', 'name', 'meeting_name', 'verify_level', 'icon_path', 'updated_at')->where('verify_level', '=', 0)->orderBy('updated_at')->paginate(6);
        } else {//如果是普通用户，只能看自己的信息
            $join_Meetings = Join_Meeting::select('id', 'user', 'name', 'meeting_name', 'verify_level', 'icon_path', 'updated_at')->where(['user' => $user, 'verify_level' => 0])->orderBy('updated_at')->paginate(6);
        }
        if (!$join_Meetings) {
            return response()->json(['status' => 402, 'msg' => 'join_Meetings required failed']);
        }
        return response()->json(['status' => 200, 'msg' => 'join_Meetings required successfully', 'name' => $account->name, 'icon_path' => $account->icon_path, 'science_level' => $account->science_level, 'data' => $join_Meetings]);
    }


    public function getDetail(Request $request)
    {
        $user = $request->input('user');
        $id = $request->input('id');
        $join_Meeting = Join_Meeting::find($id);
        if (!$join_Meeting) {
            return response()->json(['status' => 404, 'msg' => 'join_Meeting not exists']);
        }
        $account = Account::where('user', '=', $user)->first();
        if (!$account) {
            return response()->json(["status" => 404, "msg" => "user not exists"]);
        }
        return response()->json(['status' => 200, 'msg' => 'join_Meeting required successfully', 'name' => $account->name, 'icon_path' => $account->icon_path, 'science_level' => $account->science_level, 'data' => $join_Meeting]);
    }
}
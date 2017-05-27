<?php

namespace App\Http\Controllers\Science;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Account;
use App\Hold_Meeting;

class HoldMeetingController extends Controller
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
        $hold_Meeting = Hold_Meeting::create($data);
        if (!$hold_Meeting) {
            return response()->json(['status' => 402, 'msg' => 'hold_Meeting created failed']);
        }
        return response()->json(['status' => 200, 'msg' => 'hold_Meeting created successfully']);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $id = $request->input('id');
        $hold_Meeting = Hold_Meeting::find($id);
        if (!$hold_Meeting) {
            return response()->json(['status' => 404, 'msg' => 'hold_Meeting not exists']);
        }
        $hold_Meeting->update($data);
        if ($hold_Meeting->save()) {
            return response()->json(["status" => 200, "msg" => "hold_Meeting update successfully"]);
        } else {
            return response()->json(["status" => 402, "msg" => "hold_Meeting update failed"]);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $hold_Meeting = Hold_Meeting::find($id);
        if (!$hold_Meeting) {
            return response()->json(['status' => 404, 'msg' => 'hold_Meeting not exists']);
        }
        if ($hold_Meeting->delete()) {
            return response()->json(['status' => 200, 'msg' => 'hold_Meeting deleted successfully']);
        } else {
            return response()->json(['status' => 402, 'msg' => 'hold_Meeting deleted failed']);
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
                $hold_Meetings = Hold_Meeting::select('id', 'user', 'name', 'meeting_name', 'verify_level', 'icon_path', 'updated_at')->where(['verify_level' => 1,'is_domestic' => '国内'])->orderBy('updated_at')->paginate(6);
            }
            else {//如果是普通用户，只能看自己的信息
                $hold_Meetings = Hold_Meeting::select('id', 'user', 'name', 'meeting_name', 'verify_level', 'icon_path', 'updated_at')->where(['user' => $user, 'verify_level' => 1,'is_domestic' => '国内'])->orderBy('updated_at')->paginate(6);
                if (!$hold_Meetings) {
                    return response()->json(['status' => 402, 'msg' => 'hold_Meetings required failed']);
                }
            }
        }
        else{//如果参加国外会议
            if ($account->science_level) {//如果是超级用户，可以看所有表中的信息
                $hold_Meetings = Hold_Meeting::select('id', 'user', 'name', 'meeting_name', 'verify_level', 'icon_path', 'updated_at')->where(['verify_level' => 0,'is_domestic' => '国内'])->orderBy('updated_at')->paginate(6);
            }
            else {//如果是普通用户，只能看自己的信息
                $hold_Meetings = Hold_Meeting::select('id', 'user', 'name', 'meeting_name', 'verify_level', 'icon_path', 'updated_at')-where(['verify_level' => 0,'is_domestic' => '国内'])->orderBy('updated_at')->paginate(6);
                if (!$hold_Meetings) {
                    return response()->json(['status' => 402, 'msg' => 'hold_Meetings required failed']);
                }
            }
        }
        return response()->json(['status' => 200, 'msg' => 'hold_Meetings required successfully', 'name' => $account->name, 'icon_path' => $account->icon_path, 'science_level' => $account->science_level, 'data' => $hold_Meetings]);
    }


    public function getNotVerifiedIndex(Request $request)
    {//获取未审核的多个论文信息
        $user = $request->input('user');
        $account = Account::where('user', '=', $user)->first();
        if (!$account) {
            return response()->json(['status' => 404, 'msg' => 'user not exists']);
        }
        if ($account->science_level) {//如果是超级用户，可以看所有表中的信息
            $hold_Meetings = Hold_Meeting::select('id', 'user', 'name', 'meeting_name', 'verify_level', 'icon_path', 'updated_at')->where('verify_level', '=', 0)->orderBy('updated_at')->paginate(6);
        } else {//如果是普通用户，只能看自己的信息
            $hold_Meetings = Hold_Meeting::select('id', 'user', 'name', 'meeting_name', 'verify_level', 'icon_path', 'updated_at')->where(['user' => $user, 'verify_level' => 0])->orderBy('updated_at')->paginate(6);
        }
        if (!$hold_Meetings) {
            return response()->json(['status' => 402, 'msg' => 'hold_Meetings required failed']);
        }
        return response()->json(['status' => 200, 'msg' => 'hold_Meetings required successfully', 'name' => $account->name, 'icon_path' => $account->icon_path, 'science_level' => $account->science_level, 'data' => $hold_Meetings]);
    }


    public function getDetail(Request $request)
    {
        $user = $request->input('user');
        $id = $request->input('id');
        $hold_Meeting = Hold_Meeting::find($id);
        if (!$hold_Meeting) {
            return response()->json(['status' => 404, 'msg' => 'hold_Meeting not exists']);
        }
        $account = Account::where('user', '=', $user)->first();
        if (!$account) {
            return response()->json(["status" => 404, "msg" => "user not exists"]);
        }
        return response()->json(['status' => 200, 'msg' => 'hold_Meeting required successfully', 'name' => $account->name, 'icon_path' => $account->icon_path, 'science_level' => $account->science_level, 'data' => $hold_Meeting]);
    }
}

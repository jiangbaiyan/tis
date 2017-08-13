<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\Go_Abroad;
use App\Hold_Communication;
use App\Hold_Meeting;
use App\Join_Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class ActivityController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $userid = Cache::get($_COOKIE['userid']);
        $type = $request->input('activity_type');//1-参加学术会议 2-举办承办学术会议 3-举办承办学术会议 4-访学进修
        switch ($type){
            case 1:
                if (!$type||!$request->input('activity_name') || !$request->input('meeting_place') || !$request->input('meeting_time') || !$request->input('is_domestic')) {
                    return response()->json(['status' => 404, 'msg' => 'missing parameters']);
                }
                $result = Join_Meeting::create($data);
                $result->userid = $userid;
                $result->save();
                return response()->json(['status' => 200, 'msg' => 'joinMeeting created successfully']);
                break;

            case 2:
                if (!$type||!$request->input('activity_name') ||!$request->input('total_people')|| !$request->input('meeting_place') || !$request->input('meeting_time') || !$request->input('abroad_people')||!$request->input('is_domestic')) {
                    return response()->json(['status' => 404, 'msg' => 'missing parameters']);
                }
                $result = Hold_Meeting::create($data);
                $result->userid = $userid;
                $result->save();
                return response()->json(['status' => 200, 'msg' => 'holdMeeting created successfully']);
                break;

            case 3:
                if (!$type||!$request->input('activity_name')||!$request->input('start_stop_time')||!$request->input('start_stop_time')||!$request->input('work_object')){
                    return response()->json(['status' => 404, 'msg' => 'missing parameters']);
                }
                $result = Hold_Communication::create($data);
                $result->userid = $userid;
                $result->save();
                return response()->json(['status' => 200, 'msg' => 'holdCommunication created successfully']);
                break;

            case 4:
                if (!$type||!$request->input('type')||!$request->input('destination')||!$request->input('activity_name')||!$request->input('start_stop_time')){
                    return response()->json(['status' => 400, 'msg' => 'missing parameters']);
                }
                $result = Go_Abroad::create($data);
                $result->userid = $userid;
                $result->save();
                return response()->json(['status' => 200,'msg' => 'goAbroad created successfully']);
                break;
        }
    }

    public function update(Request $request){
        $data = $request->all();
        $type = $request->input('activity_type');//1-参加学术会议 2-举办承办学术会议 3-举办承办学术会议 4-访学进修
        $id = $request->input('id');
        if (!$type||!$id){
            return response()->json(['status' => 404, 'msg' => 'missing parameters']);
        }
        switch ($type){
            case 1:
                $joinMeeting = Join_Meeting::find($id);
                if (!$joinMeeting){
                    return response()->json(['status' => 439,'msg' => 'joinMeeting not found']);
                }
                if ($joinMeeting->update($data)){
                    return response()->json(["status"=>200,"msg"=>"joinMeeting update successfully"]);
                }
                else{
                    return response()->json(["status"=>478,"msg"=>"joinMeeting update failed"]);
                }
                break;

            case 2:
                $holdMeeting = Hold_Meeting::find($id);
                if (!$holdMeeting){
                    return response()->json(['status' => 439,'msg' => 'holdMeeting not found']);
                }
                if ($holdMeeting->update($data)){
                    return response()->json(["status"=>200,"msg"=>"holdMeeting update successfully"]);
                }
                else{
                    return response()->json(["status"=>478,"msg"=>"holdMeeting update failed"]);
                }
                break;

            case 3:
                $holdCommunication = Hold_Communication::find($id);
                if (!$holdCommunication){
                    return response()->json(['status' => 439,'msg' => 'holdCommunication not found']);
                }
                if ($holdCommunication->update($data)){
                    return response()->json(["status"=>200,"msg"=>"holdCommunication update successfully"]);
                }
                else{
                    return response()->json(["status"=>478,"msg"=>"holdCommunication update failed"]);
                }
                break;
            case 4:
                $goAbroad = Go_Abroad::find($id);
                if (!$goAbroad){
                    return response()->json(['status' => 439,'msg' => 'goAbroad not found']);
                }
                if ($goAbroad->update($data)){
                    return response()->json(["status"=>200,"msg"=>"goAbroad update successfully"]);
                }
                else{
                    return response()->json(["status"=>478,"msg"=>"goAbroad update failed"]);
                }
                break;
        }
    }

    public function delete(Request $request){
        $data = $request->all();
        $type = $request->input('activity_type');
        $id = $request->input('id');
        if (!$type||!$id){
            return response()->json(['status' => 404, 'msg' => 'missing parameters']);
        }
        switch ($type){
            case 1:
                $joinMeeting = Join_Meeting::find($id);
                if (!$joinMeeting){
                    return response()->json(['status' => 439,'msg' => 'joinMeeting not found']);
                }
                if ($joinMeeting->delete($data)){
                    return response()->json(["status"=>200,"msg"=>"joinMeeting delete successfully"]);
                }
                else{
                    return response()->json(["status"=>479,"msg"=>"joinMeeting delete failed"]);
                }
                break;

            case 2:
                $holdMeeting = Hold_Meeting::find($id);
                if (!$holdMeeting){
                    return response()->json(['status' => 439,'msg' => 'holdMeeting not found']);
                }
                if ($holdMeeting->delete($data)){
                    return response()->json(["status"=>200,"msg"=>"holdMeeting delete successfully"]);
                }
                else{
                    return response()->json(["status"=>479,"msg"=>"holdMeeting delete failed"]);
                }
                break;

            case 3:
                $holdCommunication = Hold_Communication::find($id);
                if (!$holdCommunication){
                    return response()->json(['status' => 439,'msg' => 'holdCommunication not found']);
                }
                if ($holdCommunication->delete($data)){
                    return response()->json(["status"=>200,"msg"=>"holdCommunication delete successfully"]);
                }
                else{
                    return response()->json(["status"=>479,"msg"=>"holdCommunication delete failed"]);
                }
                break;
            case 4:
                $goAbroad = Go_Abroad::find($id);
                if (!$goAbroad){
                    return response()->json(['status' => 439,'msg' => 'goAbroad not found']);
                }
                if ($goAbroad->delete($data)){
                    return response()->json(["status"=>200,"msg"=>"goAbroad delete successfully"]);
                }
                else{
                    return response()->json(["status"=>479,"msg"=>"goAbroad delete failed"]);
                }
                break;
        }
    }

    public function getVerifiedIndex(Request $request){
        $userid = Cache::get($_COOKIE['userid']);
        $type = $request->header('para');
        $isDomestic = $request->header('para1');
        if (!$type) {
            return response()->json(['status' => 404, 'msg' => 'missing parameters']);
        }
        $account = Account::where('userid', '=', $userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        switch ($type) {
            case 1:
                if ($isDomestic == 1) {
                    if ($account->science_level) {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.userid', '=', 'join_meeting.userid')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1,'is_Domestic' => 1])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.userid', '=', 'join_meeting.userid')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1, 'join_meeting.userid' => $userid,'is_Domestic' => 1])->orderBy('activity_name')->paginate(6);
                    }
                }
                else{
                    if ($account->science_level) {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.userid', '=', 'join_meeting.userid')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1,'is_Domestic' => 2])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.userid', '=', 'join_meeting.userid')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1, 'join_meeting.userid' => $userid,'is_Domestic' => 2])->orderBy('activity_name')->paginate(6);
                    }
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $joinMeetings]);
                break;

            case 2:
                if ($isDomestic == 1) {
                    if ($account->science_level) {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.userid', '=', 'hold_meeting.userid')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1,'is_Domestic' => 1])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.userid', '=', 'hold_meeting.userid')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1, 'hold_meeting.userid' => $userid,'is_Domestic' => 1])->orderBy('activity_name')->paginate(6);
                    }
                }
                else{
                    if ($account->science_level) {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.userid', '=', 'hold_meeting.userid')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1,'is_Domestic' => 2])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.userid', '=', 'hold_meeting.userid')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1, 'hold_meeting.userid' => $userid,'is_Domestic' => 2])->orderBy('activity_name')->paginate(6);
                    }
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $holdMeetings]);
                break;

            case 3:
                if ($account->science_level) {
                    $holdCommunications = Hold_Communication::join('accounts', 'accounts.userid', '=', 'hold_communication.userid')->select('hold_communication.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1])->orderBy('activity_name')->paginate(6);
                }
                else {
                    $holdCommunications = Hold_Communication::join('accounts', 'accounts.userid', '=', 'hold_communication.userid')->select('hold_communication.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1, 'hold_communication.userid' => $userid])->orderBy('activity_name')->paginate(6);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $holdCommunications]);
                break;

            case 4:
                if ($account->science_level) {
                    $goAbroads = Go_Abroad::join('accounts', 'accounts.userid', '=', 'go_abroad.userid')->select('go_abroad.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1])->orderBy('activity_name')->paginate(6);
                }
                else {
                    $goAbroads = Go_Abroad::join('accounts', 'accounts.userid', '=', 'go_abroad.userid')->select('go_abroad.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1, 'go_abroad.userid' => $userid])->orderBy('activity_name')->paginate(6);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $goAbroads]);
                break;
        }
    }


    public function getNotVerifiedIndex(Request $request){//获取已审核的多个论文信息
        $userid = Cache::get($_COOKIE['userid']);
        $type = $request->header('para');
        $isDomestic = $request->header('para1');
        $account = Account::where('userid', '=', $userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        switch ($type) {
            case 1:
                if ($isDomestic == 1) {
                    if ($account->science_level) {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.userid', '=', 'join_meeting.userid')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0,'is_Domestic' => 1])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.userid', '=', 'join_meeting.userid')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0, 'join_meeting.userid' => $userid,'is_Domestic' => 1])->orderBy('activity_name')->paginate(6);
                    }
                }
                else{
                    if ($account->science_level) {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.userid', '=', 'join_meeting.userid')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0,'is_Domestic' => 2])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.userid', '=', 'join_meeting.userid')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0, 'join_meeting.userid' => $userid,'is_Domestic' => 2])->orderBy('activity_name')->paginate(6);
                    }
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $joinMeetings]);
                break;

            case 2:
                if ($isDomestic == 1) {
                    if ($account->science_level) {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.userid', '=', 'hold_meeting.userid')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0,'is_Domestic' => 1])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.userid', '=', 'hold_meeting.userid')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0, 'hold_meeting.userid' => $userid,'is_Domestic' => 1])->orderBy('activity_name')->paginate(6);
                    }
                }
                else{
                    if ($account->science_level) {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.userid', '=', 'hold_meeting.userid')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0,'is_Domestic' => 2])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.userid', '=', 'hold_meeting.userid')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0, 'hold_meeting.userid' => $userid,'is_Domestic' => 2])->orderBy('activity_name')->paginate(6);
                    }
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $holdMeetings]);
                break;

            case 3:
                if ($account->science_level) {
                    $holdCommunications = Hold_Communication::join('accounts', 'accounts.userid', '=', 'hold_communication.userid')->select('hold_communication.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0])->orderBy('activity_name')->paginate(6);
                }
                else {
                    $holdCommunications = Hold_Communication::join('accounts', 'accounts.userid', '=', 'hold_communication.userid')->select('hold_communication.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0, 'hold_communication.userid' => $userid])->orderBy('activity_name')->paginate(6);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $holdCommunications]);
                break;

            case 4:
                if ($account->science_level) {
                    $goAbroads = Go_Abroad::join('accounts', 'accounts.userid', '=', 'go_abroad.userid')->select('go_abroad.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0])->orderBy('activity_name')->paginate(6);
                }
                else {
                    $goAbroads = Go_Abroad::join('accounts', 'accounts.userid', '=', 'go_abroad.userid')->select('go_abroad.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0, 'go_abroad.userid' => $userid])->orderBy('activity_name')->paginate(6);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $goAbroads]);
                break;
        }
    }


    public function getDetail(Request $request){
        $userid = Cache::get($_COOKIE['userid']);
        $type = $request->header('para');
        $id = $request->header('id');
        if (!$type||!$id){
            return response()->json(['status' => 404, 'msg' => 'missing parameters']);
        }
        $account = Account::where('userid','=',$userid)->first();
        if (!$account){
            return response()->json(['status' => 431,'msg' => 'account not found']);
        }
        switch ($type){
            case 1:
                $joinMeeting = Join_Meeting::join('accounts','accounts.userid','=','join_meeting.userid')->select('join_meeting.id','join_meeting.userid','accounts.name','verify_level','is_domestic','activity_name','meeting_place','meeting_time','remark')->find($id);
                if (!$joinMeeting){
                    return response()->json(['status' => 439,'msg' => 'joinMeeting not found']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $joinMeeting]);
                break;

            case 2:
                $holdMeeting = Hold_Meeting::join('accounts','accounts.userid','=','hold_meeting.userid')->select('hold_meeting.id','hold_meeting.userid','accounts.name','verify_level','is_domestic','activity_name','total_people','meeting_place','meeting_time','abroad_people','remark')->find($id);
                if (!$holdMeeting){
                    return response()->json(['status' => 439,'msg' => 'holdMeeting not found']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $holdMeeting]);
                break;

            case 3:
                $holdCommunication = Hold_Communication::join('accounts','accounts.userid','=','hold_communication.userid')->select('hold_communication.id','hold_communication.userid','accounts.name','verify_level','activity_name','start_stop_time','work_object','remark')->find($id);
                if (!$holdCommunication){
                    return response()->json(['status' => 439,'msg' => 'holdCommunication not found']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $holdCommunication]);
                break;
            case 4:
                $goAbroad = Go_Abroad::join('accounts','accounts.userid','=','go_abroad.userid')->select('go_abroad.id','go_abroad.userid','accounts.name','verify_level','type','destination','activity_name','start_stop_time','remark')->find($id);
                if (!$goAbroad){
                    return response()->json(['status' => 439,'msg' => 'goAbroad not found']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $goAbroad]);
                break;
        }
    }
}


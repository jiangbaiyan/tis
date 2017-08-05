<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\Go_Abroad;
use App\Hold_Communication;
use App\Hold_Meeting;
use App\Join_Meeting;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $type = $request->input('activity_type');
        switch ($type){
            case '参加学术会议':
                if (!$request->input('activity_type')||!$request->input('activity_name') || !$request->input('meeting_place') || !$request->input('meeting_time') || !$request->input('is_domestic')) {
                    return response()->json(['status' => 400, 'msg' => 'missing parameters']);
                }
                $joinMeeting = Join_Meeting::create($data);
                if (!$joinMeeting) {
                    return response()->json(['status' => 402, 'msg' => 'joinMeeting created failed']);
                }
                return response()->json(['status' => 200, 'msg' => 'joinMeeting created successfully']);
                break;

            case '举办承办学术会议':
                if (!$request->input('activity_name') ||!$request->input('total_people')|| !$request->input('meeting_place') || !$request->input('meeting_time') || !$request->input('abroad_people')||!$request->input('is_domestic')) {
                    return response()->json(['status' => 400, 'msg' => 'missing parameters']);
                }
                $holdMeeting = Hold_Meeting::create($data);
                if (!$holdMeeting) {
                    return response()->json(['status' => 402, 'msg' => 'holdMeeting created failed']);
                }
                return response()->json(['status' => 200, 'msg' => 'holdMeeting created successfully']);
                break;

            case '举办承办学术交流':
                if (!$request->input('activity_name')||!$request->input('start_stop_time')||!$request->input('start_stop_time')||!$request->input('work_object')){
                    return response()->json(['status' => 400, 'msg' => 'missing parameters']);
                }
                $holdCommunication = Hold_Communication::create($data);
                if(!$holdCommunication){
                    return response()->json(['status' => 402, 'msg' => 'holdCommunication created failed']);
                }
                return response()->json(['status' => 200, 'msg' => 'holdCommunication created successfully']);
                break;

            case '出国（境）访学进修':
                if (!$request->input('type')||!$request->input('destination')||!$request->input('activity_name')||!$request->input('start_stop_time')){
                    return response()->json(['status' => 400, 'msg' => 'missing parameters']);
                }
                $goAbroad = Go_Abroad::create($data);
                if (!$goAbroad){
                    return response()->json(['status' => 402, 'msg' => 'goAbroad created failed']);
                }
                return response()->json(['status' => 200,'msg' => 'goAbroad created successfully']);
                break;
        }
    }

    public function update(Request $request){
        $data = $request->all();
        $type = $request->input('activity_type');
        $id = $request->input('id');
        if (!$type||!$id){
            return response()->json(['status' => 400, 'msg' => 'missing parameters']);
        }
        switch ($type){
            case '参加学术会议':
                $joinMeeting = Join_Meeting::find($id);
                if (!$joinMeeting){
                    return response()->json(['status' => 404,'msg' => 'joinMeeting not exists']);
                }
                if ($joinMeeting->update($data)){
                    return response()->json(["status"=>200,"msg"=>"joinMeeting update successfully"]);
                }
                else{
                    return response()->json(["status"=>402,"msg"=>"joinMeeting update failed"]);
                }
                break;

            case '举办承办学术会议':
                $holdMeeting = Hold_Meeting::find($id);
                if (!$holdMeeting){
                    return response()->json(['status' => 404,'msg' => 'holdMeeting not exists']);
                }
                if ($holdMeeting->update($data)){
                    return response()->json(["status"=>200,"msg"=>"holdMeeting update successfully"]);
                }
                else{
                    return response()->json(["status"=>402,"msg"=>"holdMeeting update failed"]);
                }
                break;

            case '举办承办学术交流':
                $holdCommunication = Hold_Communication::find($id);
                if (!$holdCommunication){
                    return response()->json(['status' => 404,'msg' => 'holdCommunication not exists']);
                }
                if ($holdCommunication->update($data)){
                    return response()->json(["status"=>200,"msg"=>"holdCommunication update successfully"]);
                }
                else{
                    return response()->json(["status"=>402,"msg"=>"holdCommunication update failed"]);
                }
                break;
            case '出国（境）访学进修':
                $goAbroad = Go_Abroad::find($id);
                if (!$goAbroad){
                    return response()->json(['status' => 404,'msg' => 'goAbroad not exists']);
                }
                if ($goAbroad->update($data)){
                    return response()->json(["status"=>200,"msg"=>"goAbroad update successfully"]);
                }
                else{
                    return response()->json(["status"=>402,"msg"=>"goAbroad update failed"]);
                }
                break;
        }
    }

    public function delete(Request $request){
        $data = $request->all();
        $type = $request->input('activity_type');
        $id = $request->input('id');
        if (!$type||!$id){
            return response()->json(['status' => 400, 'msg' => 'missing parameters']);
        }
        switch ($type){
            case '参加学术会议':
                $joinMeeting = Join_Meeting::find($id);
                if (!$joinMeeting){
                    return response()->json(['status' => 404,'msg' => 'joinMeeting not exists']);
                }
                if ($joinMeeting->delete($data)){
                    return response()->json(["status"=>200,"msg"=>"joinMeeting delete successfully"]);
                }
                else{
                    return response()->json(["status"=>402,"msg"=>"joinMeeting delete failed"]);
                }
                break;

            case '举办承办学术会议':
                $holdMeeting = Hold_Meeting::find($id);
                if (!$holdMeeting){
                    return response()->json(['status' => 404,'msg' => 'holdMeeting not exists']);
                }
                if ($holdMeeting->delete($data)){
                    return response()->json(["status"=>200,"msg"=>"holdMeeting delete successfully"]);
                }
                else{
                    return response()->json(["status"=>402,"msg"=>"holdMeeting delete failed"]);
                }
                break;

            case '举办承办学术交流':
                $holdCommunication = Hold_Communication::find($id);
                if (!$holdCommunication){
                    return response()->json(['status' => 404,'msg' => 'holdCommunication not exists']);
                }
                if ($holdCommunication->delete($data)){
                    return response()->json(["status"=>200,"msg"=>"holdCommunication delete successfully"]);
                }
                else{
                    return response()->json(["status"=>402,"msg"=>"holdCommunication delete failed"]);
                }
                break;
            case '出国（境）访学进修':
                $goAbroad = Go_Abroad::find($id);
                if (!$goAbroad){
                    return response()->json(['status' => 404,'msg' => 'goAbroad not exists']);
                }
                if ($goAbroad->delete($data)){
                    return response()->json(["status"=>200,"msg"=>"goAbroad delete successfully"]);
                }
                else{
                    return response()->json(["status"=>402,"msg"=>"goAbroad delete failed"]);
                }
                break;
        }
    }

    public function getVerifiedIndex(Request $request){
        $user = $request->header('user');
        $type = $request->header('activity_type');
        $isDomestic = $request->header('is_domestic');
        if (!$type) {
            return response()->json(['status' => 400, 'msg' => 'missing parameters']);
        }
        $account = Account::where('user', '=', $user)->first();
        if (!$account) {
            return response()->json(['status' => 404, 'msg' => 'user not exists']);
        }
        switch ($type) {
            case '参加学术会议':
                if ($isDomestic == '国内') {
                    if ($account->science_level) {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.user', '=', 'join_meeting.user')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1,'is_Domestic' => '国内'])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.user', '=', 'join_meeting.user')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1, 'join_meeting.user' => $user,'is_Domestic' => '国内'])->orderBy('activity_name')->paginate(6);
                    }
                }
                else{
                    if ($account->science_level) {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.user', '=', 'join_meeting.user')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1,'is_Domestic' => '国外'])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.user', '=', 'join_meeting.user')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1, 'join_meeting.user' => $user,'is_Domestic' => '国外'])->orderBy('activity_name')->paginate(6);
                    }
                }
                if (!$joinMeetings) {
                    return response()->json(['status' => 402, 'msg' => 'joinMeeting required failed']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $joinMeetings]);
                break;

            case '举办承办学术会议':
                if ($isDomestic == '国内') {
                    if ($account->science_level) {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.user', '=', 'hold_meeting.user')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1,'is_Domestic' => '国内'])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.user', '=', 'hold_meeting.user')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1, 'hold_meeting.user' => $user,'is_Domestic' => '国内'])->orderBy('activity_name')->paginate(6);
                    }
                }
                else{
                    if ($account->science_level) {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.user', '=', 'hold_meeting.user')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1,'is_Domestic' => '国外'])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.user', '=', 'hold_meeting.user')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1, 'hold_meeting.user' => $user,'is_Domestic' => '国外'])->orderBy('activity_name')->paginate(6);
                    }
                }
                if (!$holdMeetings) {
                    return response()->json(['status' => 402, 'msg' => 'holdMeeting required failed']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $holdMeetings]);
                break;

            case '举办承办学术交流':
                if ($account->science_level) {
                    $holdCommunications = Hold_Communication::join('accounts', 'accounts.user', '=', 'hold_communication.user')->select('hold_communication.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1])->orderBy('activity_name')->paginate(6);
                }
                else {
                    $holdCommunications = Hold_Communication::join('accounts', 'accounts.user', '=', 'hold_communication.user')->select('hold_communication.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1, 'hold_communication.user' => $user])->orderBy('activity_name')->paginate(6);
                }
                if (!$holdCommunications) {
                    return response()->json(['status' => 402, 'msg' => 'holdCommunication required failed']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $holdCommunications]);
                break;

            case '出国（境）访学进修':
                if ($account->science_level) {
                    $goAbroads = Go_Abroad::join('accounts', 'accounts.user', '=', 'go_abroad.user')->select('go_abroad.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1])->orderBy('activity_name')->paginate(6);
                }
                else {
                    $goAbroads = Go_Abroad::join('accounts', 'accounts.user', '=', 'go_abroad.user')->select('go_abroad.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 1, 'go_abroad.user' => $user])->orderBy('activity_name')->paginate(6);
                }
                if (!$goAbroads) {
                    return response()->json(['status' => 402, 'msg' => 'goAbroad required failed']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $goAbroads]);
                break;
        }
    }


    public function getNotVerifiedIndex(Request $request){//获取已审核的多个论文信息
        $user = $request->header('user');
        $type = $request->header('activity_type');
        $isDomestic = $request->header('is_domestic');
        if (!$type) {
            return response()->json(['status' => 400, 'msg' => 'missing parameters']);
        }
        $account = Account::where('user', '=', $user)->first();
        if (!$account) {
            return response()->json(['status' => 404, 'msg' => 'user not exists']);
        }
        switch ($type) {
            case '参加学术会议':
                if ($isDomestic == '国内') {
                    if ($account->science_level) {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.user', '=', 'join_meeting.user')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0,'is_Domestic' => '国内'])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.user', '=', 'join_meeting.user')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0, 'join_meeting.user' => $user,'is_Domestic' => '国内'])->orderBy('activity_name')->paginate(6);
                    }
                }
                else{
                    if ($account->science_level) {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.user', '=', 'join_meeting.user')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0,'is_Domestic' => '国外'])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $joinMeetings = Join_Meeting::join('accounts', 'accounts.user', '=', 'join_meeting.user')->select('join_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0, 'join_meeting.user' => $user,'is_Domestic' => '国外'])->orderBy('activity_name')->paginate(6);
                    }
                }
                if (!$joinMeetings) {
                    return response()->json(['status' => 402, 'msg' => 'joinMeeting required failed']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $joinMeetings]);
                break;

            case '举办承办学术会议':
                if ($isDomestic == '国内') {
                    if ($account->science_level) {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.user', '=', 'hold_meeting.user')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0,'is_Domestic' => '国内'])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.user', '=', 'hold_meeting.user')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0, 'hold_meeting.user' => $user,'is_Domestic' => '国内'])->orderBy('activity_name')->paginate(6);
                    }
                }
                else{
                    if ($account->science_level) {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.user', '=', 'hold_meeting.user')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0,'is_Domestic' => '国外'])->orderBy('activity_name')->paginate(6);
                    }
                    else {
                        $holdMeetings = Hold_Meeting::join('accounts', 'accounts.user', '=', 'hold_meeting.user')->select('hold_meeting.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0, 'hold_meeting.user' => $user,'is_Domestic' => '国外'])->orderBy('activity_name')->paginate(6);
                    }
                }
                if (!$holdMeetings) {
                    return response()->json(['status' => 402, 'msg' => 'holdMeeting required failed']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $holdMeetings]);
                break;

            case '举办承办学术交流':
                if ($account->science_level) {
                    $holdCommunications = Hold_Communication::join('accounts', 'accounts.user', '=', 'hold_communication.user')->select('hold_communication.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0])->orderBy('activity_name')->paginate(6);
                }
                else {
                    $holdCommunications = Hold_Communication::join('accounts', 'accounts.user', '=', 'hold_communication.user')->select('hold_communication.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0, 'hold_communication.user' => $user])->orderBy('activity_name')->paginate(6);
                }
                if (!$holdCommunications) {
                    return response()->json(['status' => 402, 'msg' => 'holdCommunication required failed']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $holdCommunications]);
                break;

            case '出国（境）访学进修':
                if ($account->science_level) {
                    $goAbroads = Go_Abroad::join('accounts', 'accounts.user', '=', 'go_abroad.user')->select('go_abroad.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0])->orderBy('activity_name')->paginate(6);
                }
                else {
                    $goAbroads = Go_Abroad::join('accounts', 'accounts.user', '=', 'go_abroad.user')->select('go_abroad.id', 'accounts.name', 'accounts.icon_path', 'activity_name')->where(['verify_level' => 0, 'go_abroad.user' => $user])->orderBy('activity_name')->paginate(6);
                }
                if (!$goAbroads) {
                    return response()->json(['status' => 402, 'msg' => 'goAbroad required failed']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'data' => $goAbroads]);
                break;
        }
    }


    public function getDetail(Request $request){
        $user = $request->header('user');
        $type = $request->header('activity_type');
        $id = $request->header('id');
        if (!$type||!$id){
            return response()->json(['status' => 400, 'msg' => 'missing parameters']);
        }
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        switch ($type){
            case '参加学术会议':
                $joinMeeting = Join_Meeting::join('accounts','accounts.user','=','join_meeting.user')->select('join_meeting.id','join_meeting.user','accounts.name','verify_level','is_domestic','activity_name','meeting_place','meeting_time','remark')->find($id);
                if (!$joinMeeting){
                    return response()->json(['status' => 404,'msg' => 'joinMeeting not exists']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $joinMeeting]);
                break;

            case '举办承办学术会议':
                $holdMeeting = Hold_Meeting::join('accounts','accounts.user','=','hold_meeting.user')->select('hold_meeting.id','hold_meeting.user','accounts.name','verify_level','is_domestic','activity_name','total_people','meeting_place','meeting_time','abroad_people','remark')->find($id);
                if (!$holdMeeting){
                    return response()->json(['status' => 404,'msg' => 'holdMeeting not exists']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $holdMeeting]);
                break;

            case '举办承办学术交流':
                $holdCommunication = Hold_Communication::join('accounts','accounts.user','=','hold_communication.user')->select('hold_communication.id','hold_communication.user','accounts.name','verify_level','activity_name','start_stop_time','work_object','remark')->find($id);
                if (!$holdCommunication){
                    return response()->json(['status' => 404,'msg' => 'holdCommunication not exists']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $holdCommunication]);
                break;
            case '出国（境）访学进修':
                $goAbroad = Go_Abroad::join('accounts','accounts.user','=','go_abroad.user')->select('go_abroad.id','go_abroad.user','accounts.name','verify_level','type','destination','activity_name','start_stop_time','remark')->find($id);
                if (!$goAbroad){
                    return response()->json(['status' => 404,'msg' => 'goAbroad not exists']);
                }
                return response()->json(['status' => 200,'msg' => 'data required successfully','icon_path' => $account->icon_path,'science_level' => $account->science_level,'data' => $goAbroad]);
                break;
        }
    }
}


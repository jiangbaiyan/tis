<?php

namespace App\Http\Controllers\Info;

use App\Info_Content;
use App\Info_Feedback;
use App\Student;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class StudentInfoController extends Controller
{
    public function getIndex(){
        $openid = $_COOKIE['openid'];
        $student = Student::where('openid',$openid)->first();
        $student_id = $student->id;
        $data = Info_Content::join('info_feedbacks','info_feedbacks.info_content_id','=','info_contents.id')
            ->join('accounts','info_contents.account_id','=','accounts.userid')
            ->select('info_contents.id','info_contents.title','info_contents.created_at','accounts.name')
            ->where('info_feedbacks.student_id','=',$student_id)
            ->where('info_contents.created_at','>',date('Y-m-d H:i:s',time()-2592000))
            ->orderByDesc('info_contents.created_at')
            ->get();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $data]);
    }

    public function getDetail($id){
        $openid = $_COOKIE['openid'];
        $student = Student::where('openid',$openid)->first();
        $student_id = $student->id;
        $content = Info_Content::find($id);
        if (!$content){
            return Response::json(['status' => 404,'msg' => '内容id不存在']);
        }
        $feedback = Info_Feedback::where('info_content_id','=',$id)
            ->where('student_id','=',$student_id)
            ->first();
        $feedback->status = 1;
        $feedback->save();
        return Response::json(['status' => 200,'msg' => 'data required successfully','data' => $content]);
    }
}

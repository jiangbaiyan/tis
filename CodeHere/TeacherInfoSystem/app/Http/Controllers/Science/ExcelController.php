<?php

namespace App\Http\Controllers\Science;

use App\AcademicPartTimeJob;
use App\Account;
use App\Go_Abroad;
use App\Hold_Communication;
use App\Hold_Meeting;
use App\Join_Meeting;
use App\Literature;
use App\Patent;
use App\PlatformAndTeam;
use App\Project;
use App\ScienceAward;
use App\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function thesisExport(){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $theses = Thesis::all();
        if (!$theses){
            return response()->json(['status' => 404,'msg' => 'thesis not exists']);
        }
        foreach($theses as $thesis){
            if ($thesis->verify_level == 1){
                $thesis->verify_level = '审核通过';
            }
            else{
                $thesis->verify_level = '审核中';
            }
        }
        if ($account->science_level){
            Excel::create('论文信息表',function ($excel) use ($theses){
                $excel->sheet('论文信息表',function ($sheet) use ($theses){
                    $sheet->fromModel($theses,null,'A1',true,false);
                    $sheet->prependRow(1,['id','学号或工号','上传者','审核状态','论文名称','本人排序','论文类型','作者','期刊名称或会议名称','ISSN或ISBN号','期号','卷号','起止页码','发表时间','SCI分区','EI','CCF','国内期刊等级','SCI或EI收录检索号','杭州电子科技大学科研业绩核心指标','备注','论文路径','封面路径','上传时间','更新时间']);
                    $sheet->setWidth(array(
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  20,
                        'M'     =>  20,
                        'N'     =>  20,
                        'O'     =>  20,
                        'P'     =>  20,
                        'Q'     =>  20,
                        'R'     =>  20,
                        'S'     =>  20,
                        'T'     =>  40,
                        'U'     =>  20,
                        'V'     =>  40,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ));
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }

    public  function patentExport(){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $patents = Patent::all();
        if (!$patents){
            return response()->json(['status' => 404,'msg' => 'patent not exists']);
        }
        foreach($patents as $patent){
            if ($patent->verify_level == 1){
                $patent->verify_level = '审核通过';
            }
            else{
                $patent->verify_level = '审核中';
            }
        }
        if ($account->science_level){
            Excel::create('专利信息表',function ($excel) use ($patents){
                $excel->sheet('专利信息表',function ($sheet) use ($patents){
                    $sheet->fromModel($patents,null,'A1',true,false);
                    $sheet->prependRow(1,['id','学号或工号','上传者','审核状态','专利名称','本人排序','全部专利发明人姓名','专利类型','专利申请日','授权公告日','证书编号','专利号','杭州电子科技大学科研业绩核心指标','备注','专利路径','封面路径','上传时间','更新时间']);
                    $sheet->setWidth(array(
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  20,
                        'M'     =>  40,
                        'N'     =>  20,
                        'O'     =>  40,
                        'P'     =>  20,
                        'Q'     =>  20,
                        'R'     =>  20,
                        'S'     =>  20,
                        'T'     =>  40,
                        'U'     =>  20,
                        'V'     =>  20,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ));
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }

    public function literatureExport(){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $literatures = Literature::all();
        if (!$literatures){
            return response()->json(['status' => 404,'msg' => 'literature not exists']);
        }
        if ($account->science_level){
            Excel::create('著作和教材信息表',function ($excel) use ($literatures){
                $excel->sheet('著作和教材信息表',function ($sheet) use ($literatures){
                    $sheet->fromModel($literatures,null,'A1',true,false);
                    $sheet->prependRow(1,['id','学号或工号','上传者','审核状态','著作或教材名称','本人排序','全部作者姓名','著作或教材类型','出版社名称','出版时间','出版社类别','ISBN号','ISSN号','杭州电子科技大学科研业绩核心指标','备注','著作教材路径','封面路径','上传时间','更新时间']);
                    $sheet->setWidth(array(
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  20,
                        'M'     =>  20,
                        'N'     =>  40,
                        'O'     =>  20,
                        'P'     =>  40,
                        'Q'     =>  20,
                        'R'     =>  20,
                        'S'     =>  20,
                        'T'     =>  30,
                        'U'     =>  20,
                        'V'     =>  30,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ));
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }

    public function projectExport(){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $projects = Project::join('accounts','accounts.userid','=','projects.userid')->select('projects.id','projects.userid','accounts.name','verify_level','project_direction','project_name','project_members','project_number','project_type','project_level','project_build_time','start_stop_time','total_money','current_money','year_money','author_rank','author_task','science_core_index','remark','projects.created_at','projects.updated_at')->get();
        if (!$projects){
            return response()->json(['status' => 404,'msg' => 'project not exists']);
        }
        foreach($projects as $project){
            if ($project->verify_level == 1){
                $project->verify_level = '审核通过';
            }
            else{
                $project->verify_level = '审核中';
            }
            if ($project->project_direction == 1){
                $project->project_direction = '横向项目';
            }
            else{
                $project->project_direction = '纵向项目';
            }
        }
        if ($account->science_level){
            Excel::create('项目信息表',function ($excel) use ($projects){
                $excel->sheet('项目信息表',function ($sheet) use ($projects){
                    $sheet->fromModel($projects,null,'A1',true,false);
                    $sheet->prependRow(1,['id','学号或工号','上传者','审核状态','项目类别','项目名称','全部项目组成员姓名','项目编号','项目类型','项目级别','立项时间','起讫时间','项目总经费','已到项目经费','本年度到帐经费','本人排序','本人承担义务','杭州电子科技大学科研业绩核心指标','备注','上传时间','更新时间']);
                    $sheet->setWidth([
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  20,
                        'M'     =>  20,
                        'N'     =>  20,
                        'O'     =>  20,
                        'P'     =>  20,
                        'Q'     =>  20,
                        'R'     =>  40,
                        'S'     =>  20,
                        'T'     =>  20,
                        'U'     =>  20,
                        'V'     =>  20,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ]);
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }

    public function scienceAwardExport(){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $scienceAwards = ScienceAward::join('accounts','accounts.userid','=','scienceAwards.user')->select('scienceAwards.id','scienceAwards.user','accounts.name','verify_level','achievement_name','award_name','award_level','award_time','certificate_number','members_name','author_rank','science_core_index','remark','scienceAwards.created_at','scienceAwards.updated_at')->get();
        if (!$scienceAwards){
            return response()->json(['status' => 404,'msg' => 'project not exists']);
        }
        if ($account->science_level){
            Excel::create('科研奖励信息表',function ($excel) use ($scienceAwards){
                $excel->sheet('科研奖励信息表',function ($sheet) use ($scienceAwards){
                    $sheet->fromModel($scienceAwards,null,'A1',true,false);
                    $sheet->prependRow(1,['id','学号或工号','上传者','审核状态','科研奖励成果名称','科研奖励名称','科研奖励级别','获奖时间','证书编号','全部完成人姓名','本人排序','杭州电子科技大学科研业绩核心指标','备注','上传时间','更新时间']);
                    $sheet->setWidth([
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  40,
                        'M'     =>  20,
                        'N'     =>  20,
                        'O'     =>  20,
                        'P'     =>  20,
                        'Q'     =>  20,
                        'R'     =>  20,
                        'S'     =>  20,
                        'T'     =>  20,
                        'U'     =>  20,
                        'V'     =>  20,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ]);
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }

    public function platformAndTeamExport(Request $request){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $platformAndTeam = PlatformAndTeam::join('accounts','accounts.userid','=','platformAndTeams.user')->select('platformAndTeams.id','platformAndTeams.user','accounts.name','verify_level','group_name','author_rank','group_level','science_core_index','remark','platformAndTeams.created_at','platformAndTeams.updated_at')->get();
        if (!$platformAndTeam){
            return response()->json(['status' => 404,'msg' => 'project not exists']);
        }
        if ($account->science_level){
            Excel::create('平台和团队信息表',function ($excel) use ($platformAndTeam){
                $excel->sheet('平台和团队信息表',function ($sheet) use ($platformAndTeam){
                    $sheet->fromModel($platformAndTeam,null,'A1',true,false);
                    $sheet->prependRow(1,['id','学号或工号','上传者','审核状态','团队名称','本人排序','团队级别','杭州电子科技大学科研业绩核心指标','备注','上传时间','更新时间']);
                    $sheet->setWidth([
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  40,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  20,
                        'M'     =>  20,
                        'N'     =>  20,
                        'O'     =>  20,
                        'P'     =>  20,
                        'Q'     =>  20,
                        'R'     =>  20,
                        'S'     =>  20,
                        'T'     =>  20,
                        'U'     =>  20,
                        'V'     =>  20,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ]);
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }

    public  function joinMeetingExport(Request $request){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $joinMeeting = Join_Meeting::join('accounts','accounts.user','=','join_meeting.user')->select('join_meeting.id','join_meeting.user','accounts.name','verify_level','is_domestic','activity_name','meeting_place','meeting_time','remark','join_meeting.created_at','join_meeting.updated_at')->get();
        if (!$joinMeeting){
            return response()->json(['status' => 404,'msg' => 'project not exists']);
        }
        if ($account->science_level){
            Excel::create('参加学术会议信息表',function ($excel) use ($joinMeeting){
                $excel->sheet('参加学术会议信息表',function ($sheet) use ($joinMeeting){
                    $sheet->fromModel($joinMeeting,null,'A1',true,false);
                    $sheet->prependRow(1,['id','学号或工号','上传者','审核状态','国内（外）','会议名称','会议举办地','会议举办时间','备注','上传时间','更新时间']);
                    $sheet->setWidth([
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  20,
                        'M'     =>  20,
                        'N'     =>  20,
                        'O'     =>  20,
                        'P'     =>  20,
                        'Q'     =>  20,
                        'R'     =>  20,
                        'S'     =>  20,
                        'T'     =>  20,
                        'U'     =>  20,
                        'V'     =>  20,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ]);
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }

    public function holdMeetingExport(Request $request){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('userid','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $holdMeeting = Hold_Meeting::join('accounts','accounts.user','=','hold_meeting.user')->select('hold_meeting.id','hold_meeting.user','accounts.name','verify_level','is_domestic','activity_name','total_people','meeting_place','meeting_time','abroad_people','remark','hold_meeting.created_at','hold_meeting.updated_at')->get();
        if (!$holdMeeting){
            return response()->json(['status' => 404,'msg' => 'project not exists']);
        }
        if ($account->science_level){
            Excel::create('举办承办学术会议信息表',function ($excel) use ($holdMeeting){
                $excel->sheet('举办承办学术会议信息表',function ($sheet) use ($holdMeeting){
                    $sheet->fromModel($holdMeeting,null,'A1',true,false);
                    $sheet->prependRow(1,['id','学号或工号','上传者','审核状态','国内（外）','会议名称','参会总人数','会议举办地','会议举办时间','参会国（境）外人数','备注','上传时间','更新时间']);
                    $sheet->setWidth([
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  20,
                        'M'     =>  20,
                        'N'     =>  20,
                        'O'     =>  20,
                        'P'     =>  20,
                        'Q'     =>  20,
                        'R'     =>  20,
                        'S'     =>  20,
                        'T'     =>  20,
                        'U'     =>  20,
                        'V'     =>  20,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ]);
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }

    public function holdCommunicationExport(Request $request){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $holdCommunication = Hold_Communication::join('accounts','accounts.user','=','hold_communication.user')->select('hold_communication.id','hold_communication.user','accounts.name','verify_level','activity_name','start_stop_time','work_object','remark','hold_communication.created_at','hold_communication.updated_at')->get();
        if (!$holdCommunication){
            return response()->json(['status' => 404,'msg' => 'project not exists']);
        }
        if ($account->science_level){
            Excel::create('举办承办学术交流信息表',function ($excel) use ($holdCommunication){
                $excel->sheet('举办承办学术交流信息表',function ($sheet) use ($holdCommunication){
                    $sheet->fromModel($holdCommunication,null,'A1',true,false);
                    $sheet->prependRow(1,['id','学号或工号','上传者','审核状态','交流合作项目名称','起止时间','合作对象','备注','上传时间','更新时间']);
                    $sheet->setWidth([
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  20,
                        'M'     =>  20,
                        'N'     =>  20,
                        'O'     =>  20,
                        'P'     =>  20,
                        'Q'     =>  20,
                        'R'     =>  20,
                        'S'     =>  20,
                        'T'     =>  20,
                        'U'     =>  20,
                        'V'     =>  20,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ]);
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }

    public  function goAbroadExport(Request $request){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $goAbroad =  Go_Abroad::join('accounts','accounts.user','=','go_abroad.user')->select('go_abroad.id','go_abroad.user','accounts.name','verify_level','type','destination','activity_name','start_stop_time','remark','go_abroad.created_at','go_abroad.updated_at')->get();
        if (!$goAbroad){
            return response()->json(['status' => 404,'msg' => 'project not exists']);
        }
        if ($account->science_level){
            Excel::create('出国进修信息表',function ($excel) use ($goAbroad){
                $excel->sheet('出国进修信息表',function ($sheet) use ($goAbroad){
                    $sheet->fromModel($goAbroad,null,'A1',true,false);
                    $sheet->prependRow(1,['id','学号或工号','上传者','审核状态','出国境类型','出国（境）目的地','访学或研修机构名称','访学或研修起止时间','备注','上传时间','更新时间']);
                    $sheet->setWidth([
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  20,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  20,
                        'M'     =>  20,
                        'N'     =>  20,
                        'O'     =>  20,
                        'P'     =>  20,
                        'Q'     =>  20,
                        'R'     =>  20,
                        'S'     =>  20,
                        'T'     =>  20,
                        'U'     =>  20,
                        'V'     =>  20,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ]);
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }

    public function academicPartTimeJobExport(Request $request){
        $user = Cache::get($_COOKIE['userid']);
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $academicPartTimeJob = AcademicPartTimeJob::join('accounts','accounts.user','=','academicPartTimeJobs.user')->select('academicPartTimeJobs.id','academicPartTimeJobs.user','accounts.name','verify_level','duty','start_time','stop_time','institution_name','science_core_index','remark','academicPartTimeJobs.created_at','academicPartTimeJobs.updated_at')->get();
        if (!$academicPartTimeJob){
            return response()->json(['status' => 404,'msg' => 'project not exists']);
        }
        if ($account->science_level){
            Excel::create('学术兼职信息表',function ($excel) use ($academicPartTimeJob){
                $excel->sheet('学术兼职信息表',function ($sheet) use ($academicPartTimeJob){
                    $sheet->fromModel($academicPartTimeJob,null,'A1',true,false);
                    $sheet->prependRow(1,['id','学号或工号','上传者','审核状态','兼职职务','起始时间','结束时间','兼职学术机构名称','杭州电子科技大学科研业绩核心指标','备注','上传时间','更新时间']);
                    $sheet->setWidth([
                        'A'     =>  20,
                        'B'     =>  20,
                        'C'     =>  20,
                        'D'     =>  20,
                        'E'     =>  20,
                        'F'     =>  20,
                        'G'     =>  20,
                        'H'     =>  20,
                        'I'     =>  40,
                        'J'     =>  20,
                        'K'     =>  20,
                        'L'     =>  20,
                        'M'     =>  20,
                        'N'     =>  20,
                        'O'     =>  20,
                        'P'     =>  20,
                        'Q'     =>  20,
                        'R'     =>  20,
                        'S'     =>  20,
                        'T'     =>  20,
                        'U'     =>  20,
                        'V'     =>  20,
                        'W'     =>  20,
                        'X'     =>  20,
                        'Y'     =>  20,
                        'Z'     =>  20,
                    ]);
                    $sheet->cells('A1:Z99', function($cells) {
                        $cells->setAlignment('center');
                    });
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }
}

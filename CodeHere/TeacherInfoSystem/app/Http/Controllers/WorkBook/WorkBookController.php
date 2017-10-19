<?php

namespace App\Http\Controllers\WorkBook;

use App\Account;
use App\Teacher_note0_shouye;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpWord\TemplateProcessor;

class WorkBookController extends Controller
{
    public function write(Request $request){
        //$userid = Cache::get($_COOKIE['userid']);
        $data = $request->all();
        $teacher = Account::where('userid','=','15051141')->first();
        $templateProcessor = new TemplateProcessor('storage/template/template.docx');
       /* $templateProcessor->setValue('0_qishi',$request->input('0_qishi'));
        $templateProcessor->setValue('0_jieshu',$request->input('0_jieshu'));
        $templateProcessor->setValue('0_xueqi',$request->input('0_xueqi'));
        $templateProcessor->setValue('0_xingming',$request->input('0_xingming'));
        $templateProcessor->setValue('0_mingcheng',$request->input('0_mingcheng'));
        $templateProcessor->setValue('0_kehao',$request->input('0_kehao'));
        $templateProcessor->setValue('0_xingzhi',$request->input('0_xingzhi'));
        $templateProcessor->setValue('0_duixiang',$request->input('0_duixiang'));
        $templateProcessor->setValue('0_shijian',$request->input('0_shijian'));
        */
        $templateProcessor->setValue('1_xuefen',$request->input('1_xuefen'));
        $templateProcessor->setValue('1_zhouxueshi',$request->input('1_zhouxueshi'));
        $templateProcessor->setValue('1_zongxueshi',$request->input('1_zongxueshi'));
        $templateProcessor->setValue('1_jiangshou',$request->input('1_jiangshou'));
        $templateProcessor->setValue('1_shiyan',$request->input('1_shiyan'));
        $templateProcessor->setValue('1_shangji',$request->input('1_shangji'));
        $templateProcessor->setValue('1_shijian',$request->input('1_shijian'));
        $templateProcessor->setValue('1_zixue',$request->input('1_zixue'));
        $templateProcessor->setValue('1_ISBN',$request->input('1_ISBN'));
        $templateProcessor->setValue('1_jiaocaimingcheng',$request->input('1_jiaocaimingcheng'));
        $templateProcessor->setValue('1_chubanshe',$request->input('1_chubanshe'));
        $templateProcessor->setValue('1_zixue',$request->input('1_zixue'));
        $templateProcessor->setValue('1_cankaoshumu',$request->input('1_cankaoshumu'));
        $templateProcessor->setValue('1_kaoqin',$request->input('1_kaoqin'));
        $templateProcessor->setValue('1_zuoye',$request->input('1_zuoye'));
        $templateProcessor->setValue('1_qimo',$request->input('1_qimo'));

        $templateProcessor->saveAs("storage/results/$teacher->userid.docx");

        /*
        //数据库存储
        $newArr0 = array_where($data,function ($value,$key){
                return substr($key,0,2) == '0_';
        });

        $newArr1 = array_where($data,function ($value,$key){
            return substr($key,0,2) == '1_';
        });
        Teacher_note0_shouye::create($newArr0);
        */

        return Response::json(['status' => 200,'msg' => 'data saved successfully']);
    }
}

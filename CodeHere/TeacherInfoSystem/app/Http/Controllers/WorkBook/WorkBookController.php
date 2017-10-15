<?php

namespace App\Http\Controllers\WorkBook;

use App\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpWord\TemplateProcessor;

class WorkBookController extends Controller
{
    public function write(Request $request){
        //$userid = Cache::get($_COOKIE['userid']);
        $picture = $request->file('picture');
        $teacher = Account::where('userid','=','15075119')->first();
        $templateProcessor = new TemplateProcessor('storage/template/template.docx');
        $templateProcessor->setValue('教师姓名',$teacher->name);
        $templateProcessor->setValue('课程名称','课程名称');
        $templateProcessor->setValue('图片',$picture);
        $templateProcessor->setValue('选课课号','选课课号');
        $templateProcessor->setValue('课程性质','课程性质');
        $templateProcessor->setValue('开课对象','课程对象');
        $templateProcessor->setValue('填表时间',date('Y-m-d H:i:s',time()));
        $templateProcessor->saveAs("storage/results/$teacher->name.docx");
    }
}

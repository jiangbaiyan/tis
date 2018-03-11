<?php

namespace App\Http\Controllers;


use App\Account;
use App\Http\Controllers\LoginAndAccount\Controller;
use App\Info_Content;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller//单元测试控制器
{
    const URL = 'https://cloudfiles.cloudshm.com/';

    public function test(Request $request)
    {
        $teacher = Account::where('userid', '=', '15075119')->first();
        if (!$teacher) {
            return Response::json(['status' => 404, 'msg' => 'user not exists']);
        }
        $name = $teacher->name;
        $file = $request->file('file');
        $fname = $file->getClientOriginalName();//获取文件名
        $path = Storage::disk('upyun')->putFileAs('file/' . "$name" .'/'. date('Y') . '/' . date('md'), $file, $fname, 'public');
        $url = self::URL . $path;
        $teacher->files()->create(['name' => $fname, 'url' => $url]);
        return Response::json(['status' => 200, 'msg' => 'success']);
    }
}

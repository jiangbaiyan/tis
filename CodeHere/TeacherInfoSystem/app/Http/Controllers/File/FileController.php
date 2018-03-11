<?php

namespace App\Http\Controllers\File;

use App\Account;
use App\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\LoginAndAccount\Controller;

class FileController extends Controller
{
    //文件上传
    public function uploadFiles(Request $request)
    {
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid', $userid)->first();
        if (!$teacher) {
            return Response::json(['status' => 404, 'msg' => 'user not exists']);
        }
        $name = $request->input('filename ');
        $url = $request->input('url');
        $teacher->files()->create(['name' => $name, 'url' => $url]);
        return Response::json(['status' => 200, 'msg' => 'success']);
    }

    //普通老师查看自己的文件列表
    public function getMyFiles(){
        $userid = Cache::get($_COOKIE['userid']);
        $teacher = Account::where('userid', $userid)->first();
        if (!$teacher) {
            return Response::json(['status' => 404, 'msg' => 'user not exists']);
        }
        $data = $teacher->files()->get();
        return Response::json(['status' => 200,'msg' => 'success','data' => $data]);
    }

    //教务老师查看某个老师的文件列表
    public function getTeachersFiles($id){
        $teacher = Account::find($id);
        if (!$teacher) {
            return Response::json(['status' => 404, 'msg' => 'user not exists']);
        }
        $data = $teacher->files()->get();
        return Response::json(['status' => 200,'msg' => 'success','data' => $data]);
    }

    //删除文件
    public function deleteFile($id){
        $file = File::find($id);
        try {
            $file->delete();
        } catch (\Exception $e) {
            return Response::json(['status' => 402,'msg' => 'file deleted failed']);
        }
        return Response::json(['status' => 200,'msg' => 'success']);
    }
}

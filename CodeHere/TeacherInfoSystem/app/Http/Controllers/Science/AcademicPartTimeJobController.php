<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\AcademicPartTimeJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AcademicPartTimeJobController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $user = $request->input('user');
        if (!$request->input('duty')){
            return response()->json(['status' => 400,'msg' => 'need duty']);
        }
        $academicPartTimeJob = academicPartTimeJob::create($data);
        if (!$academicPartTimeJob) {
            return response()->json(['status' => 402, 'msg' => 'academicPartTimeJob created failed']);
        }
        return response()->json(['status' => 200,'msg' => 'academicPartTimeJob created successfully']);
    }

    public function update(Request $request){
        $data = $request->all();
        $id = $request->input('id');
        $academicPartTimeJob = academicPartTimeJob::find($id);
        if (!$academicPartTimeJob){
            return response()->json(['status' => 404,'msg' => 'academicPartTimeJob not exists']);
        }
        $academicPartTimeJob->update($data);
        if($academicPartTimeJob->save()) {
            return response()->json(["status"=>200,"msg"=>"academicPartTimeJob update successfully"]);
        }
        else {
            return response()->json(["status"=>402,"msg"=>"academicPartTimeJob update failed"]);
        }
    }

    public function delete(Request $request){
        $id = $request->input('id');
        $academicPartTimeJob = academicPartTimeJob::find($id);
        if (!$academicPartTimeJob){
            return response()->json(['status' => 404,'msg' => 'academicPartTimeJob not exists']);
        }
        if ($academicPartTimeJob->delete()){
            return response()->json(['status' => 200,'msg' => 'academicPartTimeJob deleted successfully']);
        }
        else {
            return response()->json(['status' => 402,'msg' => 'academicPartTimeJob deleted failed']);
        }
    }

    public function getIndex(Request $request){
        $user = $request->input('user');
        $academicPartTimeJobs = academicPartTimeJob::where('user','=',$user)->paginate(6);
        $account = Account::where('user','=',$user)->first();
        if(!$account) {
            return response()->json(["status"=>404,"msg"=>"user not exists"]);
        }
        return response()->json(['status'=>200,"msg"=>"academicPartTimeJobs required successfully",'name' => $account->name,'icon_path' => $account->icon_path,'data'=>$academicPartTimeJobs]);
    }

}

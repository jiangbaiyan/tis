<?php

namespace App\Http\Controllers\Science;

use App\Account;
use App\Thesis;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function thesisExport(Request $request){
        $user = $request->input('user');
        $account = Account::where('user','=',$user)->first();
        if (!$account){
            return response()->json(['status' => 404,'msg' => 'user not exists']);
        }
        $theses = Thesis::all();
        if (!$theses){
            return response()->json(['status' => 404,'msg' => 'theses not exists']);
        }
        if ($account->science_level){
            Excel::create('论文信息表',function ($excel) use ($theses){
                $excel->sheet('论文信息表',function ($sheet) use ($theses){
                    $sheet->fromModel($theses);
                });
            })->export('xlsx');
        }
        else{
            return response()->json(['status' => 402,'msg' => 'Permission denied']);
        }
        return response()->json(['status' => 200,'msg' => 'Excel exported successfully']);
    }
}

<?php

namespace App\Http\Controllers\Leave;

use App\Leave_info;;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class LeaveInfoController extends Controller
{
    public function create(Request $request){
        $data = $request->all();
        $userid = Cache::get($_COOKIE['userid']);
        $leave_info = Leave_info::create(['userid' => $userid,'title' => $request->input('title'),'from' => $request->input('from'),'to' => $request->input('to')]);
        if (!$leave_info){
            return Response::json(['status' => 402,'msg' => 'create failed']);
        }
        return Response::json(['status' => 200,'msg' => 'created successfully']);
    }
}

<?php

namespace App\Http\Controllers\Science;

use App\Patent;
use Illuminate\Http\Request;

class PatentController extends Controller
{
    public function get(Request $request){
        $user = $request->input('user');
        $patents = Patent::where('user','=',$user)->get();
        if ($patents->isEmpty()){
            Patent::create(['user' => $user]);
        }
        return response()->json(['status' => 200,'msg' => 'patents required successfully','date' => $patents]);
    }

    public function update(Request $request){

    }
}

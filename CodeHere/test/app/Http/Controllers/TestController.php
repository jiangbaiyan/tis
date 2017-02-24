<?php
/**
 * Created by PhpStorm.
 * User: yangbingyan
 * Date: 17-2-9
 * Time: 下午4:18
 */

namespace App\Http\Controllers;


use App\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $input= $request->all();

        $user = new Test();

        $validate = $user->checkValidate($input);

        if($validate->fails()){
            $warnings = $validate->messages();
            $show_warning = $warnings->first();
            return response()->json($warnings);
            //print_r($show_warning);
        }
    }
}
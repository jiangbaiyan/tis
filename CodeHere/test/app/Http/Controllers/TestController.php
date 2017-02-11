<?php
/**
 * Created by PhpStorm.
 * User: yangbingyan
 * Date: 17-2-9
 * Time: 下午4:18
 */

namespace App\Http\Controllers;


use App\EmailUser;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $input= $request->all();

        $user = new EmailUser();

        $exist = $user->isExist($input);

        return response()->json($exist);
    }
}
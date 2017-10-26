<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\LoginAndAccount\Controller;

class TestController extends Controller
{
    public function testPDF(Request $request){
        $file = $request->file('test');
        $postData = ['file' => $file];
        $ch = curl_init('121.41.51.133:2002');
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postData);
        $result = curl_exec($ch);
        curl_close($ch);
        dd($result);
    }
}

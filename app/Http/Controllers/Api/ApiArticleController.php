<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Articles;

class ApiArticleController extends Controller
{

    public $articles;
    public $request;

    public function __construct(Request $request, Articles $articles)
    {
        $this->request = $request;
        $this->articles = $articles;
    }


    public function verify()
    {
        //获取验证参数
        $timestamp = $this->request->get('time', 0);
        $sysCode =  $this->request->get('code', '');
        $safeCode = $this->request->get('token', '');
        $privateCode = 'newsfeed';

        if (md5($timestamp . $sysCode . $privateCode) != $safeCode) {
            return false;
        }
        return true;

    }

    public function index()
    {
        $this->verify();
        $data  = $this->articles->all()->toArray();
        return response()->json($data);

    }
}

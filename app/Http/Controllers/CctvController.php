<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Articles;

class CctvController extends Controller
{


 public $articles;

    public function __construct(Articles $articles)
    {
        $this->articles = $articles;
    }


    public function test()
    {
        $data = $this->articles->allData();
        var_dump( $data );

    }
}

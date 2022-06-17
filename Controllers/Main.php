<?php

namespace App\Controllers;

use App\Controllers\Auth;
use App\Models\DB;
use App\Query;
use App\View;
use App\Lang;
use App\Formater;
use App\Router;
use App\Opengraph;


class Main
{

    function Index(Query $Request){
        $data=Lang::LOAD("index");
      
        View::Show("main",$data);
    }

}
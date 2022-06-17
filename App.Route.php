<?php

namespace App {

    use App\Autoloader;
    use App\Router;
    use App\Query;
    use App\View;
    use App\Controllers as C;
    use App\Models;
    use App;

//R::get("*/page/[id]/config/bbb*",function(Q $Request){});


// здесь вф можете описывать роуты которые вы хотите использовать в веб приложении


Router::get("*/page/[id]/config/bbb*",function(Query $Request){
var_dump($Request);

});



}
<?php
/**
 * Роуты приложения
 * 
 * Данная конструкция описывает все роуты которые будут отлавливаться и обробатываться движком
 *
 * Пример
 * Router::get("/page/[id]/config/bbb*",function(Query $Request){........}); // отлов диномичного параметра $id
 * Router::get("/",function(Query $Request){........});
 * Router::get("/main",function(Query $Request){........});
 * 
 * @copyright IntroZorn (c) 2022, Хроленко П.А.
 */
namespace App {

    use App\Autoloader;
    use App\Router;
    use App\Query;
    use App\View;
    use App\Controllers as C;
    use App\Models;
    use App;


Router::newget("/[key0]/sdfsdaf/[key1]/['(.*)']","Main->Index");

Router::get("*/",function(Query $Request){


});



}
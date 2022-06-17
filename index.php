<?php
namespace App{


use App\Router;
use App\Autoloader;



if (file_exists('config.php')) {
    require_once('config.php'); //подгружаем конфиги
}else{
    require_once('config_prod.php'); //подгружаем конфиги  продакшина 
}


session_name('IZ_HASH');  //устанавливаем параметры сессии
session_set_cookie_params(SESSION_LIFE);
session_start();


require_once("Engine/AUTOLOADER.php"); //подгружаем автозагрузчик

Autoloader::Autoload();

require_once('COMPRESSOR.php'); //подгружаем конфиги компрессора
Router::ifREAL();

require_once("App.Route.php"); // подгружаем роуты


}
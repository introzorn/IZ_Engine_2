<?php
namespace App;
use App\Router;
// здесь в массиве можно указать какие файлы необходимо принудительно сжимать
// пока реализован механизм только сжатия css b js
// указывать необходимо от view тоесть view/css/style.js
// Router::COMPRESSOR([

//     "view/js/animhead.js",
//     "view/js/script.js",
//     "view/css/style.css",
//     "view/css/mystyle.css",

//  ]);


Router::COMPRESSOR([

    "view/js/animhead.js",
    "view/js/script.js",
    "view/css/style.css",
    "view/css/mystyle.css",


 ]);


//===========================












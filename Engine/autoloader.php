<?php


namespace App;

use App\Controllers;
use App\Models;

class Autoloader
{

	private static $modules = [
	

		'ISQL.php', //MYSQL посредник проекта
		'MIME.php', //майм типы
		'Router.php', //роутер проекта
		'Query.php', //запрос к серверу
		'Validator.php', //полключаем валидаторы
		'View.php', //полключаем вью
		'Model.php', //полключаем модель
		'Lang.php', //полключаем языки
		'Formater.php', //Форматирование строк
		'Mailer.php', //Почтовый агент
		//'Opengraph.php', //Разметка опенграф
		//'Auth.php', //полключаем Авторизацию
	];

	public function __construct()
	{
	}


	public static function initMOD()
	{
		for ($i = 0; $i < sizeof(self::$modules); $i++) {
			require_once('./Engine/' . self::$modules[$i]); //подгрузка модулей

		}
	}


	public static function Autoload()
	{


		self::initMOD();

		//загружаем контролеры
		$filelist = glob("Controllers/*.php");

		foreach ($filelist as $filename) {
			require_once('./' . $filename);
		}
		//загружаем модели
		$filelist = glob("Models/*.php");

		foreach ($filelist as $filename) {
			require_once('./' . $filename);
		}
	}
}

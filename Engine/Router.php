<?php

namespace App;

use App\Controllers as C;
use App\Models;
use App\Query;
//класс для роутинга
class Router
{	
	private static $COMPRSTR;



	public static function RealizeURL(String $method,String $pattern,  $callable ,$domain=false){
	
		
		if ($_SERVER['REQUEST_METHOD'] != strtoupper($method) && $_SERVER['REQUEST_METHOD'] != 'HEAD' && strtoupper($method)!="ALL") {
			return;
		}

		$pattern = trim($pattern);

		if ($pattern == "") {return;}

		$requestParts  = explode('?', $_SERVER['REQUEST_URI']);
		$keys["_request_string"]="";
		
		if($domain==true){$requestParts[0]=$_SERVER['HTTP_HOST'].$requestParts[0];}

	
		if (strpos($pattern,"[") || strpos($pattern,"']") ){
			$patternArray=explode("/",$pattern);
			
			$iterat=1;
		
			$patternArray=array_map(function($item) use(&$keys,&$iterat,&$nextItem) {
				
				if(preg_match("/(\[\'(.*?)'\])/",$item,$match)){
					$part=explode("||",$match[2]);
					sizeof($part)==0?$part[]="":false;
					trim($part[1])==""?$part[1]="_param".$iterat++:false;
					$keys[]=$part[1];
					$str= preg_quote(str_replace($match[0],"%param%",$item));
					return str_replace("%param%",$part[0],$str);
					

				}else if (preg_match("/(\[([^'][^\W]\w*?[^'])\])/",$item,$match)){

					$keys[]=$match[2];
					$str= preg_quote(str_replace($match[0],"%param%",$item));
					return str_replace("%param%","(.*)",$str);

				}

			
				return $item;
			},$patternArray);


			$pattern=join('\\/',$patternArray);

		}else{
			
			$pattern=preg_quote($pattern);
			
		}

		
		if($pattern[0].$pattern[1] == "\\*"){$pattern[0]=" ";$pattern[1]=" ";}else{$pattern="^".$pattern;}
		if($pattern[strlen($pattern) - 1].$pattern[strlen($pattern) - 2]=="\\*" ){
			$pattern[strlen($pattern) - 1]=" ";
		    $pattern[strlen($pattern) - 2]=" ";
		}else{$pattern .= "?";}


	

		$pattern="/".trim($pattern)."/";
	
		if(preg_match($pattern,$requestParts[0],$matches)){

			$reqPram=array_combine($keys,$matches);
			$_GET=array_merge ($reqPram, $_GET);
			$Request = new Query($reqPram);
			if (gettype($callable)=="string"){
				if(strpos($callable,"::")>0){
					$fn="App\\Controllers\\".$callable;
					$fn($Request);
					die;
				}

				$clb=explode("->",$callable);
				$cls="App\\Controllers\\".$clb[0];
				$controller= new $cls;
			    $fn=$clb[1];

				
				$controller->$fn($Request);
				die;
			}
			$callable($Request);
			die;
		}	

	

	return;

	}


	private static function realisePatern(){
		
	}




	//роут для гет запросов
	public static function get(string $rPath, $fx)
	{
		if ($_SERVER['REQUEST_METHOD'] != 'GET') {
			return;
		}
		$ReqA  = explode('?', $_SERVER['REQUEST_URI']);
		$url = $ReqA[0];
		$l = "^";
		$r = "$";
		if ($rPath[0] == "*") {
			$l = "";
			$rPath = mb_substr($rPath, 1, strlen($rPath) - 1);
		}
		if ($rPath[strlen($rPath) - 1] == "*") {
			$r = "";
			$rPath = mb_substr($rPath, 0, strlen($rPath) - 1);
		}
		if (preg_match_all("/\[([^\[\]]+)\]/", $rPath, $keys)) {
			$rPath = preg_quote($rPath, "/");
			$rPath = str_replace("\[", "[", $rPath);
			$rPath = str_replace("\]", "]", $rPath);
			$rPath = preg_replace("/\[([^\[\]]+)\]/", "(.*)", $rPath);
			if (preg_match_all("/" . $l . $rPath . $r . "/", $url, $values)) {
				$val = self::refixArray($values);
				$reqPram = array_combine($keys[1], $val);
				$_GET=array_merge ($reqPram, $_GET);
				$Request = new Query($reqPram);
				$fx($Request);
				return;
			}
		}

		$pPath = preg_quote($rPath, "/");

		if (preg_match_all('/' . $l . $pPath . $r . '/', $url)) {
			$Request = new Query([]);
			$fx($Request);
			return;
		}
	}

	//роут для пост запросов
	public static function post(string $rPath, $fx)
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			return;
		}
		$ReqA  = explode('?', $_SERVER['REQUEST_URI']);
		$url = $ReqA[0];
		$l = "^";
		$r = "$";
		if ($rPath[0] == "*") {
			$l = "";
			$rPath = mb_substr($rPath, 1, strlen($rPath) - 1);
		}
		if ($rPath[strlen($rPath) - 1] == "*") {
			$r = "";
			$rPath = mb_substr($rPath, 0, strlen($rPath) - 1);
		}
		if (preg_match_all("/\[([^\[\]]+)\]/", $rPath, $keys)) {
			$rPath = preg_quote($rPath, "/");
			$rPath = str_replace("\[", "[", $rPath);
			$rPath = str_replace("\]", "]", $rPath);
			$rPath = preg_replace("/\[([^\[\]]+)\]/", "(.*)", $rPath);
			if (preg_match_all("/" . $l . $rPath . $r . "/", $url, $values)) {
				$val = self::refixArray($values);
				$reqPram = array_combine($keys[1], $val);
				$_GET=array_merge ($reqPram, $_GET);
				$Request = new Query($reqPram);
				$fx($Request);
				return;
			}
		}

		$pPath = preg_quote($rPath, "/");

		if (preg_match_all('/' . $l . $pPath . $r . '/', $url)) {
			$Request = new Query([]);
			$fx($Request);
			return;
		}
	}


	//роуты по доменам


	public static function Dget(string $rPath, $fx)
	{
		if ($_SERVER['REQUEST_METHOD'] != 'GET') {
			return;
		}
		$ReqA  = explode('?', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		$url = $ReqA[0];
		$l = "^";
		$r = "$";
		if ($rPath[0] == "*") {
			$l = "";
			$rPath = mb_substr($rPath, 1, strlen($rPath) - 1);
		}
		if ($rPath[strlen($rPath) - 1] == "*") {
			$r = "";
			$rPath = mb_substr($rPath, 0, strlen($rPath) - 1);
		}
		if (preg_match_all("/\[([^\[\]]+)\]/", $rPath, $keys)) {
			$rPath = preg_quote($rPath, "/");
			$rPath = str_replace("\[", "[", $rPath);
			$rPath = str_replace("\]", "]", $rPath);
			$rPath = preg_replace("/\[([^\[\]]+)\]/", "(.*)", $rPath);
			if (preg_match_all("/" . $l . $rPath . $r . "/", $url, $values)) {
				$val = self::refixArray($values);
				$reqPram = array_combine($keys[1], $val);
				$_GET=array_merge ($reqPram, $_GET);
				$Request = new Query($reqPram);
				$fx($Request);
				return;
			}
		}

		$pPath = preg_quote($rPath, "/");

		if (preg_match_all('/' . $l . $pPath . $r . '/', $url)) {
			$Request = new Query([]);
			$fx($Request);
			return;
		}
	}

	public static function Dpost(string $rPath, $fx)
	{
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			return;
		}
		$ReqA  = explode('?', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		$url = $ReqA[0];
		$l = "^";
		$r = "$";
		if ($rPath[0] == "*") {
			$l = "";
			$rPath = mb_substr($rPath, 1, strlen($rPath) - 1);
		}
		if ($rPath[strlen($rPath) - 1] == "*") {
			$r = "";
			$rPath = mb_substr($rPath, 0, strlen($rPath) - 1);
		}
		if (preg_match_all("/\[([^\[\]]+)\]/", $rPath, $keys)) {
			$rPath = preg_quote($rPath, "/");
			$rPath = str_replace("\[", "[", $rPath);
			$rPath = str_replace("\]", "]", $rPath);
			$rPath = preg_replace("/\[([^\[\]]+)\]/", "(.*)", $rPath);
			if (preg_match_all("/" . $l . $rPath . $r . "/", $url, $values)) {
				$val = self::refixArray($values);
				$reqPram = array_combine($keys[1], $val);
				$_GET=array_merge ($reqPram, $_GET);
				$Request = new Query($reqPram);
				$fx($Request);
				return;
			}
		}

		$pPath = preg_quote($rPath, "/");

		if (preg_match_all('/' . $l . $pPath . $r . '/', $url)) {
			$Request = new Query([]);
			$fx($Request);
			return;
		}
	}




	private static function refixArray($arr)
	{
		$rtArr = [];
		for ($i = 1; $i < sizeof($arr); $i++) {
			array_push($rtArr, $arr[$i][0]);
		}
		return $rtArr;
	}




	public static function ifREAL()
	{ //проверка если запрос это реальный файл 

		$base = explode('/', $_SERVER['SCRIPT_NAME']);
		$base[count($base) - 1] = "";
		$basepath = join("/", $base);


		$ReqA  = explode('?', $_SERVER['REQUEST_URI']);
		$ReqA[0] = str_replace($basepath, '/', $ReqA[0]);
		$Furl = 'View' . $ReqA[0];
		$fta = explode('.', $Furl);

		$Ftype = strtolower($fta[sizeof($fta) - 1]);

		if (file_exists($Furl) && is_file($Furl)) {
			if ($Ftype == 'php') {
				require_once($Furl);
				die();
			} else {

				header('accept-ranges: bytes');
				header('Content-Description: File Transfer');
				//header('Content-Type: ' . mime_content_type($Furl));
				header('Content-Type: ' . iMIME::GetMIME('.' . $Ftype));
				//header('Content-Disposition: attachment; filename=' . basename($Furl));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($Furl));
				if(self::if_COMP($Furl)){
					
					$minfile=self::DO_COMPRESS($Furl);
					header('Content-Length: ' . strlen($minfile));
					echo($minfile);
					die;
				}
				readfile($Furl);
				die();
			}
		}
	}


	//тут реализован механизм компрессора
	public static function COMPRESSOR($array)
	{
		self::$COMPRSTR=$array;
	}
	private static function if_COMP($file){
		return in_array($file,self::$COMPRSTR);
	}
	public static function DO_COMPRESS($file){
		$buf=file_get_contents($file);
		$fta = explode('.', $file);
		$Ftype = strtolower($fta[sizeof($fta) - 1]);

		if ($Ftype=='css'){return self::compress_css($buf);}
		if ($Ftype=='js'){return self::compress_js($buf);}
		return $buf;
	}

	private static  function compress_css($buffer) {
		
		$buffer = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $buffer);
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", "  ", "    ", "    "), "", $buffer);
		return $buffer;
	}

	private  static   function compress_js($buffer) {
		$buffer = preg_replace("/\/\/[^\n]*/", "", $buffer);
		$buffer = preg_replace("/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/", "", $buffer);
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", "  ", "    ", "    "), "", $buffer);

		return $buffer;
	}






	//редирект по необходимости
	public static function Redirect($url)
	{

		if (!strpos('http://', $url) || !strpos('https://', $url)) {
			$url = self::GetBaseUrl() . $url;
		}

		header('location: ' . $url);
		die();
	}

	public static function GetBaseUrl()
	{
		
		$base = explode('/', $_SERVER['SCRIPT_NAME']);
		$base[count($base) - 1] = "";
		
		$baseURL = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'].'/' ;//. join("/", $base);
	//	if(defined('BASE_REPLACE')){$baseURL=str_replace(constant('BASE_REPLACE'),"/",$baseURL);}
		return $baseURL;
	}
	public static function GetBaseUrl2()
	{
		$base = explode('/', $_SERVER['SCRIPT_NAME']);
		$base[count($base) - 1] = "";

		$baseURL = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . join("/", $base);
		return $baseURL;
	}


	public static function JSON_Response($jsonDATA, $flag=0){
	
		$resp=json_encode($jsonDATA, $flag);
		if(!$resp){$resp='{error:"bad json"}';}
		header('Content-type: application/json');
		die($resp);
	}
	public static function TEXT_Response($TextDATA){
		header('Content-type: text/plain');
		die($TextDATA);
	}
	public static function Error($ErrNum, $ErrText){
	
		switch ($ErrNum) {
			case 100: $text = 'Continue'; break;
			case 101: $text = 'Switching Protocols'; break;
			case 200: $text = 'OK'; break;
			case 201: $text = 'Created'; break;
			case 202: $text = 'Accepted'; break;
			case 203: $text = 'Non-Authoritative Information'; break;
			case 204: $text = 'No Content'; break;
			case 205: $text = 'Reset Content'; break;
			case 206: $text = 'Partial Content'; break;
			case 300: $text = 'Multiple Choices'; break;
			case 301: $text = 'Moved Permanently'; break;
			case 302: $text = 'Moved Temporarily'; break;
			case 303: $text = 'See Other'; break;
			case 304: $text = 'Not Modified'; break;
			case 305: $text = 'Use Proxy'; break;
			case 400: $text = 'Bad Request'; break;
			case 401: $text = 'Unauthorized'; break;
			case 402: $text = 'Payment Required'; break;
			case 403: $text = 'Forbidden'; break;
			case 404: $text = 'Not Found'; break;
			case 405: $text = 'Method Not Allowed'; break;
			case 406: $text = 'Not Acceptable'; break;
			case 407: $text = 'Proxy Authentication Required'; break;
			case 408: $text = 'Request Time-out'; break;
			case 409: $text = 'Conflict'; break;
			case 410: $text = 'Gone'; break;
			case 411: $text = 'Length Required'; break;
			case 412: $text = 'Precondition Failed'; break;
			case 413: $text = 'Request Entity Too Large'; break;
			case 414: $text = 'Request-URI Too Large'; break;
			case 415: $text = 'Unsupported Media Type'; break;
			case 500: $text = 'Internal Server Error'; break;
			case 501: $text = 'Not Implemented'; break;
			case 502: $text = 'Bad Gateway'; break;
			case 503: $text = 'Service Unavailable'; break;
			case 504: $text = 'Gateway Time-out'; break;
			case 505: $text = 'HTTP Version not supported'; break;
			default:
			$text = 'Bad Request'; break;
			break;
		}



		header("HTTP/1.0 $ErrNum $text");
	
		die($ErrText);
	}

}

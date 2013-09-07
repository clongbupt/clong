<?php
//!index.php 总入口
 /**
 *
 */

date_default_timezone_set("Asia/Shanghai");

ini_set('display_errors','On');
error_reporting(E_ALL);

define('APP_PATH','../app/'); //with trailing slash pls      //控制器、视图、模型所在位置
define('WEB_FOLDER','/library/public/'); //with trailing slash pls  请带上斜线    //网站根目录的位置  相对路径
define('LIB_PATH','../lib/');
define('WEB_DOMAIN','http://localhost'); //with http:// and NO trailing slash pls
define('VIEW_PATH','../app/view/'); //with trailing slash pls       //视图相对路径


require_once(LIB_PATH.'Model.php');
require_once(LIB_PATH.'View.php');
require_once(LIB_PATH.'Controller.php');
require_once(APP_PATH.'config/functions.php');

function __autoload($classname) {
  $a=$classname[0];
  $b=$classname[strlen($classname)-1];
  if ($a >= 'A' && $a <='Z'){
	if ($b == 'r' && $classname != "User")
		require_once(APP_PATH.'controller/'.$classname.'.php');  //自动识别controller类
	else
		require_once(APP_PATH.'model/'.$classname.'.php');    //自动识别model类
  }
  else{
	$c = substr($classname,0,2);
	if ($c == "db")
		require_once('../lib/db/'.$classname.'.class.php');    //自动识别db类
	}
}



//根据$_GET["action"]取值的不同调用不同的控制器子类
// if (isset($_GET['action'])) $action = $_GET['action'];
// else $action = "index";

// switch ($action)
// {
	// case 'index':
		// $controller = & new IndexController(); break;
	// case 'result':
		// $controller = & new ResultController(); break;
	// case 'add':
	// case 'detail':
	// case 'addSubmit':
		// $controller = & new AddCommentController(); break;
	// case 'addRecommend':
	// case 'addRecommendSubmit':
		// $controller = & new RecommendController(); break;
	 // default:
		 // $controller = & new RedirectController(); break;
// }
Controller::parse_request();

<?php
function myUrl($url='',$fullurl=true) {    //当前设置资源路径为全路径
  $s=$fullurl ? WEB_DOMAIN : '';
  $s.=WEB_FOLDER.$url;
  return $s;
}

function redirect($url,$alertmsg='') {
  if ($alertmsg)
    addjAlert($alertmsg,$url);
  header('Location: '.myUrl($url));
  exit;
}

function require_login() {
  if (!isset($_SESSION['authuid']))
    redirect('main/login');
}
/**
	 * 静态加载项目设置目录(config目录)中的配置文件
	 * 
	 * 加载项目设置目录(config)中的配置文件,当第一次加载后,第二次加载时则不再重新加载文件
	 * @access public
	 * @param string $file_name 所要加载的配置文件名 注：不含后缀名
	 * @return mixed	配置文件内容
	 */
	function load_config($file_name) {
		
		//参数分析.
		if (!$file_name) {
			return false;
		}
		static $_config = array();			
		if (empty($_config[$file_name])) {					
			$file_url = APP_PATH.'config/'.$file_name . '.php';		
			//判断文件是否存在
			if (!is_file($file_url)) {			
				var_dump('The config file:' . $file_name . '.class is not exists!');						
			}			
			$_config[$file_name] = include_once $file_url;
		}
				
		return $_config[$file_name];
	}
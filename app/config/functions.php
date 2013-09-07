<?php
function myUrl($url='',$fullurl=true) {    //��ǰ������Դ·��Ϊȫ·��
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
	 * ��̬������Ŀ����Ŀ¼(configĿ¼)�е������ļ�
	 * 
	 * ������Ŀ����Ŀ¼(config)�е������ļ�,����һ�μ��غ�,�ڶ��μ���ʱ�������¼����ļ�
	 * @access public
	 * @param string $file_name ��Ҫ���ص������ļ��� ע��������׺��
	 * @return mixed	�����ļ�����
	 */
	function load_config($file_name) {
		
		//��������.
		if (!$file_name) {
			return false;
		}
		static $_config = array();			
		if (empty($_config[$file_name])) {					
			$file_url = APP_PATH.'config/'.$file_name . '.php';		
			//�ж��ļ��Ƿ����
			if (!is_file($file_url)) {			
				var_dump('The config file:' . $file_name . '.class is not exists!');						
			}			
			$_config[$file_name] = include_once $file_url;
		}
				
		return $_config[$file_name];
	}
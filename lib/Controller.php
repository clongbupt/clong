<?php
 //! Controller
  /**
  * 控制器将$_GET['action']中不同的参数（list、update、team）
  * 对应于完成该功能控制的相应子类
  */
class Controller {
	public $view;
    //! 构造函数
    /**
    * 构造一个Model对象存储于成员变量$this->model;
    */
    function __construct () {
		$this->view = & new View();
	}
	
	public static function parse_request(){
		
		static $actionArray = array();
		
		$requri = $_SERVER['REQUEST_URI'];
		if (strpos($requri,WEB_FOLDER)===0)    //默认web_folder为/kiss_demo/
			$requri=substr($requri,strlen(WEB_FOLDER));     //去掉前面的web_folder部分
		
		$requri = substr($requri,0,strlen($requri)-4);      //去掉后面的.php
		
		$request_uri_parts = $requri ? explode('/',$requri) : array();   //explode返回一个数组，例如地址为localhost/kiss_demo/main/index   得到(main,index)数组
		                                                                   //例如地址为localhost/kiss_demo/main/index/param1/param2   得到(main,index,param1,param2)数组
		$params = array();
		$p = $request_uri_parts;    //按照常理  p一般包含两部分  第一部分为controller，第二部分为action 可能带有参数(params)
		if (isset($p[0]) && $p[0])   //设置p[0]，且p[0]存在
			$controller=$p[0];
		else
			$controller = 'index';
			
		if (isset($p[1]) && $p[1])
			$action=$p[1];
		else
			$action = 'index';
			
		if (isset($p[2])){
			$temp=array_slice($p,2);   //从第二个开始将数据放入params数组中

			if (is_array($temp)&&(count($temp)%2 == 0))
				 for ($i=0;$i<count($temp);$i+=2)
					 $params[$temp[$i]] = $temp[$i+1];
		}	
		//将params解开
		
	
		$actionId = $controller.'_'.$action;

		if (empty($actionArray[$actionId])) {
			
			$a = strtoupper($controller[0]);
			$controller = substr($controller,1);
			$controllerfile=$a.$controller.'Controller';
			$actionfile = $action.'Action';
			
			$controllerObject = new $controllerfile;
			
			$actionArray[$actionId] = $controllerObject->$actionfile($params);
		}

	}
	
	/**
	 * 网址(URL)组装操作
	 * 
	 * 组装绝对路径的URL
	 * @access public
	 * @param string 	$route 			controller与action
	 * @param array 	$params 		URL路由其它字段
	 * @param boolean 	$routing_mode	网址是否启用路由模式
	 * @return string	URL
	 */
	public function redirect($controller,$action, $params = '') {
	    
		$url = $_SERVER['REQUEST_URI'];
		if (strpos($url,WEB_FOLDER)===0)    //默认web_folder为/kiss_demo/
			$url=substr($url,0,strlen(WEB_FOLDER)-1);     //去掉前面的web_folder部分
		$url .= '/'.$controller .'/'.$action;
		if (!empty($params))
			foreach ($params as $k => $v)
				$url .= '/'.$k.'/'.$v;
		$url .= '.php';
		echo $url;
		header ("Location:".$url);
	}
	
	/**
	 * Ajax调用返回
	 * 
	 * 返回json数据,供前台ajax调用
	 * @param array $data	返回数组,支持数组
	 * @param string $info	返回信息, 默认为空
	 * @param boolean $status	执行状态, 1为true, 0为false
	 * @return string
	 */
	public function ajaxReturn($info = null, $status = 1, $data = array()) {
		
		$result 			= array();
		$result['status'] 	= $status;
		$result['info']		= (!is_null($info)) ? $info : '';
		$result['data']		= $data;
		
		header("Content-Type:text/html; charset=utf-8");
        exit(json_encode($result));
	}
	
	
}
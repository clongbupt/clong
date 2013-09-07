<?php
/**
 * db_mysqli.class.php
 * 
 * db_mysqli数据库驱动,完成对mysql数据库的操作
 * @package core
 * @author tommy <streen003@gmail.com>
 * @copyright Copyright (c) 2010 Tommy Software Studio
 * @link http://www.doitphp.com
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id:db_mysqli.class.php 1.0 2010-10-27 20:58:00Z tommy $
 * @since 1.0
 */

class db_mysqli{
	
	/**
	 * 单例模式实例化本类
	 * 
	 * @var object
	 */
	public static $instance;
	
	/**
	 * 数据库连接ID
	 * 
	 * @var object
	 */
	public $db_link;
	
	/**
	 * 事务处理开启状态
	 * 
	 * @var boolean
	 */
	public $Transactions;
	
	
	/**
	 * 构造函数
	 * 
	 * 用于初始化运行环境,或对基本变量进行赋值
	 * @access public
	 * @param array $params 数据库连接参数,如主机名,数据库用户名,密码等
	 * @return boolean
	 */
	public function __construct($params = array()) {

		//检测参数信息是否完整
		if (!$params['host'] || !$params['username'] || !$params['password'] || !$params['dbname']) {			
			var_dump('Database Server:HostName or UserName or Password or Database Name is error in the config file!');
		}		
		
		//实例化mysql连接ID
		$this->db_link = $params['port'] ? @new mysqli($params['host'], $params['username'], $params['password'], $params['dbname'], $params['port']) : @new mysqli($params['host'], $params['username'], $params['password'], $params['dbname']);
		
		if (mysqli_connect_errno()) {
			//当调试模式开启时(DOIT_DEBUG为true时).
			// if (DOIT_DEBUG === true) {				
				 var_dump('Mysql Server connect fail.<br/>Error Message:'.mysqli_connect_error().'<br/>Error Code:' . mysqli_connect_errno(), 'Warning');
			// } else {
				// var_dump('Mysql Server connect fail. Error Code:' . mysqli_connect_errno() . ' Error Message:' . mysqli_connect_error(), 'Warning');
				// var_dump('Mysql Server connect fail!');
			// }			
		} else {
			//设置数据库编码
			$this->db_link->query("SET NAMES {$params['charset']}");
			
			$sql_version = $this->get_server_info();			
			if (version_compare($sql_version,'5.0.2','>=')) {				
				$this->db_link->query("SET SESSION SQL_MODE=''");
			}
		}
		
		return true;
	}
	
	/**
	 * 执行SQL语句
	 * 
	 * SQL语句执行函数.
	 * @access public
	 * @param string $sql SQL语句内容
	 * @return mixed
	 */
	public function query($sql) {
		
		//参数分析
		if (!$sql) {
			return false;
		}
		
		//获取执行结果
		$result = $this->db_link->query($sql);

		//日志操作,当调试模式开启时,将所执行过的SQL写入SQL跟踪日志文件,便于DBA进行MYSQL优化.若调试模式关闭,当SQL语句执行错误时写入日志文件
		//if (DOIT_DEBUG === true) {
			//获取当前运行的controller及action名称
			//$controller_id		= doit::get_controller_id();
			//$action_id			= doit::get_action_id();			
			//$sql_log_file		= APP_ROOT . 'logs/' . 'SQL_' . date('Y_m_d', $_SERVER['REQUEST_TIME']) . '.log';
			
			// if ($result == true) {
				// var_dump('[' . $controller_id . '][' . $action_id . ']:' . $sql, 'Normal', $sql_log_file);
			// } else {
				// var_dump('[' . $controller_id . '][' . $action_id . '] SQL execute failed :' . $sql . '<br/>Error Message:' . $this->error() . '<br/>Error Code:' . $this->errno() . '<br/>Error SQL:'.$sql);
			// }
		// } else {
			// if ($result == false) {
				// //获取当前运行的controller及action名称
				// $controller_id		= doit::get_controller_id();
				// $action_id			= doit::get_action_id();
				
				// var_dump('[' . $controller_id . '][' . $action_id . '] SQL execute failed :' . $sql . ' Error Code:' . $this->errno() . 'Error Message:' . $this->error());
				// var_dump('SQL语句执行错误,详细情况请查看日志!');
			// }
		// }
		
		return $result;
	}
	
	/**
	 * 获取mysql数据库服务器信息
	 * 
	 * @access public
	 * @return string
	 */
	public function get_server_info() {
		//当没有mysql连接时
		if (!$this->db_link) {
			return false;
		}
		
		return $this->db_link->server_version;
	}
	
	/**
	 * 获取mysql错误描述信息
	 * 
	 * @access public
	 * @return string
	 */
	public function error() {
		
		return $this->db_link->error;
	}
	
	/**
	 * 获取mysql错误信息代码
	 * 
	 * @access public
	 * @return int
	 */
	public function errno() {
		
		return $this->db_link->errno;
	}
	
	/**
	 * 通过一个SQL语句获取一行信息(字段型)
	 * 
	 * @access public
	 * @param string $sql SQL语句内容
	 * @return mixed
	 */
	public function fetch_row($sql) {
		
		//参数分析
		if (!$sql) {
			return false;
		}
		
		//执行SQL语句
		$result = $this->query($sql);
		
		if(!$result){
			return false;
		}
		
		$rows = $result->fetch_assoc();
		//清空不必要的内存占用			
		$result->free();
		
		return $rows;
	}
	
	/**
	 * 通过一个SQL语句获取全部信息(字段型)
	 * 
	 * @access public
	 * @param string $sql SQL语句
	 * @return array
	 */
	public function get_array($sql) {
		
		//参数分析
		if (!$sql) {
			return false;
		}
		
		//执行SQL语句.
		$result = $this->query($sql);
				
		if (!$result) {			
			return false;
		}
		
		$myrow = array();
		while ($row = $result->fetch_assoc()) {				
			$myrow[] = $row;				
		}			
		$result->free();
		
		return $myrow;
	}
	
	/**
	 * 获取insert_id
	 * 
	 * @access public
	 * @return int
	 */
	public function insert_id(){
		
		return ($id = $this->db_link->insert_id) >= 0 ? $id :$this->query("SELECT last_insert_id()")->fetch_row();
	}
	
	
	/**
	 * 析构函数
	 * 
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		
		//关闭数据库连接
		if ($this->db_link) {
			@$this->db_link->close();
		}
	}
	
	/**
	 * 单例模式
	 * 
	 * 用于本类的单例模式(singleton)实例化
	 * @access public
	 * @param array $params 数据库连接参数,如数据库服务器名,用户名,密码等
	 * @return object
	 */
	public function getInstance($params) {		
		
		if (!self::$instance) {			
			self::$instance = new self($params);
		}
		
		return self::$instance;
	}
}
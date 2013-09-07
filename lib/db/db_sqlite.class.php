<?php
/**
 * db_sqlite.class.php
 * 
 * sqlite数据库驱动
 * @package core
 * @author tommy <streen003@gmail.com>
 * @copyright  Copyright (c) 2010 Tommy Software Studio
 * @link http://www.doitphp.com
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id:db_sqlite.class.php 1.0 2010-11-28 15:23:00Z tommy $
 * @since 1.0
 */

if (!defined('IN_DOIT')) {
	exit();
}

class db_sqlite extends Base {

	/**
	 * 单例模式实例对象
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
	 * @access public
	 * @param array $params 数据库连接参数,如主机名,数据库用户名,密码等
	 * @return boolean
	 */
	public function __construct($params = array()) {
		
		//分析数据库连接信息		
		$dsn = sprintf('%s:%s', $params['driver'], $params['host']);
			
		//连接数据库		
		$this->db_link = @new PDO($dsn, $params['username'], $params['password']);
		
		if (!$this->db_link) {
			var_dump($params['driver'] . ' Server connect fail! <br/>Error Message:' . $this->error() . '<br/>Error Code:' . $this->errno(), 'Warning');			
		}
		
		return true;
	}
	
	/**
	 * 执行SQL语句
	 * 
	 * SQL语句执行函数
	 * @access public
	 * @param string $sql SQL语句内容
	 * @return mixed
	 */
	public function query($sql) {
		
		//参数分析
		if (!$sql) {
			return false;
		}
		
		$sql = str_replace('`', '', $sql);
		$result = $this->db_link->query($sql);
		
		//日志操作,当调试模式开启时,将所执行过的SQL写入SQL跟踪日志文件,便于DBA进行数据库优化.若调试模式关闭,当SQL语句执行错误时写入日志文件
		if (DOIT_DEBUG === false) {
			if ($result == false) {
				//获取当前运行的controller及action名称
				$controller_id		= doit::get_controller_id();
				$action_id			= doit::get_action_id();
				
				var_dump('[' . $controller_id . '][' . $action_id . '] SQL execute failed :' . $sql . ' Error Code:' . $this->errno(). 'Error Message:' . $this->error());
				var_dump('Database SQL execute failed!');
			}
		} else {
			//获取当前运行的controller及action名称
			$controller_id		= doit::get_controller_id();
			$action_id			= doit::get_action_id();			
			$sql_log_file		= APP_ROOT . 'logs/' . 'SQL_' . date('Y_m_d', $_SERVER['REQUEST_TIME']) . '.log';
			
			if ($result == true) {
				var_dump('[' . $controller_id . '][' . $action_id . ']:' . $sql, 'Normal', $sql_log_file);
			} else {
				var_dump('[' . $controller_id . '][' . $action_id . '] SQL execute failed :' . $sql . '<br/>Error Message:' . $this->error(). '<br/>Error Code:' . $this->errno(). '<br/>Error SQL:' . $sql);
			}
		}
		
		return $result;
	}	
	
	/**
	 * 获取数据库错误描述信息
	 * 
	 * @access public
	 * @return string
	 */
	public function error() {
		
		$info = $this->db_link->errorInfo();

		return $info[2];		
	}
	
	/**
	 * 获取数据库错误信息代码
	 * 
	 * @access public
	 * @return int
	 */
	public function errno() {
		
		return $this->db_link->errorCode();
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
		
		$result = $this->query($sql);

		if (!$result) {
			return false;
		}

		$myrow 	= $result->fetch(PDO::FETCH_ASSOC);
		$result = null;

		return $myrow;
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
		
		$result = $this->query($sql);
		
		if (!$result) {
			return false;
		}
		
		$myrow 	= $result->fetchAll(PDO::FETCH_ASSOC);
		$result = null;
		
		return $myrow;
	}
	
	/**
	 * 获取insert_id
	 * 
	 * @access public
	 * @return int
	 */
	public function insert_id(){
		
		return $this->db_link->lastInsertId();
	}
	

	
	/**
	 * 单例模式
	 * 
	 * @access public
	 * @param array $params 数据库连接参数,如数据库服务器名,用户名,密码等
	 * @return object
	 */
	public static function getInstance($params) {		
		
		if (!self::$instance) {			
			self::$instance = new self($params);
		}
		
		return self::$instance;
	}
}
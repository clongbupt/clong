<?php
/**
 * db_mysqli.class.php
 * 
 * db_mysql数据库驱动,完成对mysql数据库的操作
 * @package core
 * @author tommy <streen003@gmail.com>
 * @copyright Copyright (c) 2010 Tommy Software Studio
 * @link http://www.doitphp.com
 * @license New BSD License.{@link http://www.opensource.org/licenses/bsd-license.php}
 * @version $Id:db_mysql.class.php 1.0 2010-11-27 22:35:00Z tommy $
 * @since 1.0
 */

if (!defined('IN_DOIT')) {
	exit();
}

class db_mysql extends Base {
	
	/**
	 * 单例模式实例化对象
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
			var_dump('Mysql Server HostName or UserName or Password or DatabaseName is error in the config file!');
		}		
		
		//处理数据库端口
		if ($params['port'] && $params['port'] != 3306) {
			$params['host'] .= ':' . $params['port'];
		}			
		
		//实例化mysql连接ID
		$this->db_link = @mysql_connect($params['host'], $params['username'], $params['password']);
		
		if (!$this->db_link) {
			var_dump('Mysql Server connect fail! <br/>Error Message:' . mysql_error() . '<br/>Error Code:' . mysql_errno(), 'Warning');		
		} else {
			if (mysql_select_db($params['dbname'], $this->db_link)) {
				//设置数据库编码
				mysql_query("SET NAMES {$params['charset']}", $this->db_link);
				if (version_compare($this->get_server_info(), '5.0.2', '>=')) {
					mysql_query("SET SESSION SQL_MODE=''", $this->db_link);
				}
			} else {
				//连接错误,提示信息
				var_dump('Mysql Server can not connect database table. Error Code:' . mysql_errno() . ' Error Message:' . mysql_error(), 'Warning');				
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
		if (!$sql || !$this->db_link) {
			return false;
		}
		
		$result = mysql_query($sql, $this->db_link);
		
		//日志操作,当调试模式开启时,将所执行过的SQL写入SQL跟踪日志文件,便于DBA进行MYSQL优化。若调试模式关闭,当SQL语句执行错误时写入日志文件
		if (DOIT_DEBUG === false) {
			if ($result == false) {
				//获取当前运行的controller及action名称
				$controller_id		= doit::get_controller_id();
				$action_id			= doit::get_action_id();
				
				var_dump('[' . $controller_id . '][' . $action_id . '] SQL execute failed :' . $sql . ' Error Code:' . $this->errno() . 'Error Message:'.$this->error());
				var_dump('数据库脚本执行错误!');
			}			
		} else {
			//获取当前运行的controller及action名称
			$controller_id		= doit::get_controller_id();
			$action_id			= doit::get_action_id();			
			$sql_log_file		= APP_ROOT . 'logs/' . 'SQL_' . date('Y_m_d', $_SERVER['REQUEST_TIME']) . '.log';
			
			if ($result == true) {
				var_dump('[' . $controller_id . '][' . $action_id . ']:' . $sql, 'Normal', $sql_log_file);
			} else {
				var_dump('[' . $controller_id . '][' . $action_id . '] SQL execute failed :' . $sql . '<br/>Error Message:' . $this->error() . '<br/>Error Code:'.$this->errno(). '<br/>Error SQL:'.$sql);
			} 
		}
		
		return $result;
	}
	
	/**
	 * 获取mysql数据库服务器信息
	 * 
	 * @access public
	 * @return string
	 */
	public function get_server_info() {
		
		if (!$this->db_link) {
			return false;
		}
		
		return mysql_get_server_info($this->db_link);
	}
	
	/**
	 * 获取mysql错误描述信息
	 * 
	 * @access public
	 * @return string
	 */
	public function error() {

		return ($this->db_link) ? mysql_error($this->db_link) : mysql_error();
	}
	
	/**
	 * 获取mysql错误信息代码
	 * 
	 * @access public
	 * @return int
	 */
	public function errno() {

		return ($this->db_link) ? mysql_errno($this->db_link) : mysql_errno();
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
		
		$rows = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
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
		
		$result = $this->query($sql);
		
		if (!$result) {
			return false;
		}
		
		$myrow = array();
		while ($row = mysql_fetch_assoc($result)) {
			$myrow[] = $row;
		}
		mysql_free_result($result);
		
		return $myrow;
	}
	
	/**
	 * 获取insert_id
	 * 
	 * @access public
	 * @return int
	 */
	public function insert_id() {
		
		return ($id = mysql_insert_id($this->db_link)) >= 0 ? $id : mysql_result($this->query("SELECT last_insert_id()"));
	}

	
	/**
	 * 析构函数
	 * 
	 * @access public
	 * @return void
	 */
	public function __destruct() {

		if ($this->db_link) {
			@mysql_close($this->db_link);
		}		
	}
	
	/**
	 * 单例模式
	 * 
	 * @access public
	 * @param array $params 数据库连接参数
	 * @return object
	 */
	public static function getInstance($params) {
		
		if (!self::$instance) {			
			self::$instance = new self($params);
		}
		
		return self::$instance;
	}
}
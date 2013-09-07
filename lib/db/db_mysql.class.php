<?php
/**
 * db_mysqli.class.php
 * 
 * db_mysql���ݿ�����,��ɶ�mysql���ݿ�Ĳ���
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
	 * ����ģʽʵ��������
	 * 
	 * @var object
	 */
	public static $instance;
	
	/**
	 * ���ݿ�����ID
	 * 
	 * @var object
	 */
	public $db_link;
	
	/**
	 * ��������״̬
	 * 
	 * @var boolean
	 */
	public $Transactions;
	
	
	/**
	 * ���캯��
	 * 
	 * ���ڳ�ʼ�����л���,��Ի����������и�ֵ
	 * @access public
	 * @param array $params ���ݿ����Ӳ���,��������,���ݿ��û���,�����
	 * @return boolean
	 */
	public function __construct($params = array()) {

		//��������Ϣ�Ƿ�����
		if (!$params['host'] || !$params['username'] || !$params['password'] || !$params['dbname']) {			
			var_dump('Mysql Server HostName or UserName or Password or DatabaseName is error in the config file!');
		}		
		
		//�������ݿ�˿�
		if ($params['port'] && $params['port'] != 3306) {
			$params['host'] .= ':' . $params['port'];
		}			
		
		//ʵ����mysql����ID
		$this->db_link = @mysql_connect($params['host'], $params['username'], $params['password']);
		
		if (!$this->db_link) {
			var_dump('Mysql Server connect fail! <br/>Error Message:' . mysql_error() . '<br/>Error Code:' . mysql_errno(), 'Warning');		
		} else {
			if (mysql_select_db($params['dbname'], $this->db_link)) {
				//�������ݿ����
				mysql_query("SET NAMES {$params['charset']}", $this->db_link);
				if (version_compare($this->get_server_info(), '5.0.2', '>=')) {
					mysql_query("SET SESSION SQL_MODE=''", $this->db_link);
				}
			} else {
				//���Ӵ���,��ʾ��Ϣ
				var_dump('Mysql Server can not connect database table. Error Code:' . mysql_errno() . ' Error Message:' . mysql_error(), 'Warning');				
			}
		}
		
		return true;
	}
	
	/**
	 * ִ��SQL���
	 * 
	 * SQL���ִ�к���.
	 * @access public
	 * @param string $sql SQL�������
	 * @return mixed
	 */
	public function query($sql) {
		
		//��������
		if (!$sql || !$this->db_link) {
			return false;
		}
		
		$result = mysql_query($sql, $this->db_link);
		
		//��־����,������ģʽ����ʱ,����ִ�й���SQLд��SQL������־�ļ�,����DBA����MYSQL�Ż���������ģʽ�ر�,��SQL���ִ�д���ʱд����־�ļ�
		if (DOIT_DEBUG === false) {
			if ($result == false) {
				//��ȡ��ǰ���е�controller��action����
				$controller_id		= doit::get_controller_id();
				$action_id			= doit::get_action_id();
				
				var_dump('[' . $controller_id . '][' . $action_id . '] SQL execute failed :' . $sql . ' Error Code:' . $this->errno() . 'Error Message:'.$this->error());
				var_dump('���ݿ�ű�ִ�д���!');
			}			
		} else {
			//��ȡ��ǰ���е�controller��action����
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
	 * ��ȡmysql���ݿ��������Ϣ
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
	 * ��ȡmysql����������Ϣ
	 * 
	 * @access public
	 * @return string
	 */
	public function error() {

		return ($this->db_link) ? mysql_error($this->db_link) : mysql_error();
	}
	
	/**
	 * ��ȡmysql������Ϣ����
	 * 
	 * @access public
	 * @return int
	 */
	public function errno() {

		return ($this->db_link) ? mysql_errno($this->db_link) : mysql_errno();
	}
	
	/**
	 * ͨ��һ��SQL����ȡһ����Ϣ(�ֶ���)
	 * 
	 * @access public
	 * @param string $sql SQL�������
	 * @return mixed
	 */
	public function fetch_row($sql) {
		
		//��������
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
	 * ͨ��һ��SQL����ȡȫ����Ϣ(�ֶ���)
	 * 
	 * @access public
	 * @param string $sql SQL���
	 * @return array
	 */
	public function get_array($sql) {
		
		//��������
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
	 * ��ȡinsert_id
	 * 
	 * @access public
	 * @return int
	 */
	public function insert_id() {
		
		return ($id = mysql_insert_id($this->db_link)) >= 0 ? $id : mysql_result($this->query("SELECT last_insert_id()"));
	}

	
	/**
	 * ��������
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
	 * ����ģʽ
	 * 
	 * @access public
	 * @param array $params ���ݿ����Ӳ���
	 * @return object
	 */
	public static function getInstance($params) {
		
		if (!self::$instance) {			
			self::$instance = new self($params);
		}
		
		return self::$instance;
	}
}
<?php
/**
 * db_mysqli.class.php
 * 
 * db_mysqli���ݿ�����,��ɶ�mysql���ݿ�Ĳ���
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
			var_dump('Database Server:HostName or UserName or Password or Database Name is error in the config file!');
		}		
		
		//ʵ����mysql����ID
		$this->db_link = $params['port'] ? @new mysqli($params['host'], $params['username'], $params['password'], $params['dbname'], $params['port']) : @new mysqli($params['host'], $params['username'], $params['password'], $params['dbname']);
		
		if (mysqli_connect_errno()) {
			//������ģʽ����ʱ(DOIT_DEBUGΪtrueʱ).
			// if (DOIT_DEBUG === true) {				
				 var_dump('Mysql Server connect fail.<br/>Error Message:'.mysqli_connect_error().'<br/>Error Code:' . mysqli_connect_errno(), 'Warning');
			// } else {
				// var_dump('Mysql Server connect fail. Error Code:' . mysqli_connect_errno() . ' Error Message:' . mysqli_connect_error(), 'Warning');
				// var_dump('Mysql Server connect fail!');
			// }			
		} else {
			//�������ݿ����
			$this->db_link->query("SET NAMES {$params['charset']}");
			
			$sql_version = $this->get_server_info();			
			if (version_compare($sql_version,'5.0.2','>=')) {				
				$this->db_link->query("SET SESSION SQL_MODE=''");
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
		if (!$sql) {
			return false;
		}
		
		//��ȡִ�н��
		$result = $this->db_link->query($sql);

		//��־����,������ģʽ����ʱ,����ִ�й���SQLд��SQL������־�ļ�,����DBA����MYSQL�Ż�.������ģʽ�ر�,��SQL���ִ�д���ʱд����־�ļ�
		//if (DOIT_DEBUG === true) {
			//��ȡ��ǰ���е�controller��action����
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
				// //��ȡ��ǰ���е�controller��action����
				// $controller_id		= doit::get_controller_id();
				// $action_id			= doit::get_action_id();
				
				// var_dump('[' . $controller_id . '][' . $action_id . '] SQL execute failed :' . $sql . ' Error Code:' . $this->errno() . 'Error Message:' . $this->error());
				// var_dump('SQL���ִ�д���,��ϸ�����鿴��־!');
			// }
		// }
		
		return $result;
	}
	
	/**
	 * ��ȡmysql���ݿ��������Ϣ
	 * 
	 * @access public
	 * @return string
	 */
	public function get_server_info() {
		//��û��mysql����ʱ
		if (!$this->db_link) {
			return false;
		}
		
		return $this->db_link->server_version;
	}
	
	/**
	 * ��ȡmysql����������Ϣ
	 * 
	 * @access public
	 * @return string
	 */
	public function error() {
		
		return $this->db_link->error;
	}
	
	/**
	 * ��ȡmysql������Ϣ����
	 * 
	 * @access public
	 * @return int
	 */
	public function errno() {
		
		return $this->db_link->errno;
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
		
		//ִ��SQL���
		$result = $this->query($sql);
		
		if(!$result){
			return false;
		}
		
		$rows = $result->fetch_assoc();
		//��ղ���Ҫ���ڴ�ռ��			
		$result->free();
		
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
		
		//ִ��SQL���.
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
	 * ��ȡinsert_id
	 * 
	 * @access public
	 * @return int
	 */
	public function insert_id(){
		
		return ($id = $this->db_link->insert_id) >= 0 ? $id :$this->query("SELECT last_insert_id()")->fetch_row();
	}
	
	
	/**
	 * ��������
	 * 
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		
		//�ر����ݿ�����
		if ($this->db_link) {
			@$this->db_link->close();
		}
	}
	
	/**
	 * ����ģʽ
	 * 
	 * ���ڱ���ĵ���ģʽ(singleton)ʵ����
	 * @access public
	 * @param array $params ���ݿ����Ӳ���,�����ݿ��������,�û���,�����
	 * @return object
	 */
	public function getInstance($params) {		
		
		if (!self::$instance) {			
			self::$instance = new self($params);
		}
		
		return self::$instance;
	}
}
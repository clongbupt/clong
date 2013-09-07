<?php
/**
 * db_sqlite.class.php
 * 
 * sqlite���ݿ�����
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
	 * ����ģʽʵ������
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
	 * @access public
	 * @param array $params ���ݿ����Ӳ���,��������,���ݿ��û���,�����
	 * @return boolean
	 */
	public function __construct($params = array()) {
		
		//�������ݿ�������Ϣ		
		$dsn = sprintf('%s:%s', $params['driver'], $params['host']);
			
		//�������ݿ�		
		$this->db_link = @new PDO($dsn, $params['username'], $params['password']);
		
		if (!$this->db_link) {
			var_dump($params['driver'] . ' Server connect fail! <br/>Error Message:' . $this->error() . '<br/>Error Code:' . $this->errno(), 'Warning');			
		}
		
		return true;
	}
	
	/**
	 * ִ��SQL���
	 * 
	 * SQL���ִ�к���
	 * @access public
	 * @param string $sql SQL�������
	 * @return mixed
	 */
	public function query($sql) {
		
		//��������
		if (!$sql) {
			return false;
		}
		
		$sql = str_replace('`', '', $sql);
		$result = $this->db_link->query($sql);
		
		//��־����,������ģʽ����ʱ,����ִ�й���SQLд��SQL������־�ļ�,����DBA�������ݿ��Ż�.������ģʽ�ر�,��SQL���ִ�д���ʱд����־�ļ�
		if (DOIT_DEBUG === false) {
			if ($result == false) {
				//��ȡ��ǰ���е�controller��action����
				$controller_id		= doit::get_controller_id();
				$action_id			= doit::get_action_id();
				
				var_dump('[' . $controller_id . '][' . $action_id . '] SQL execute failed :' . $sql . ' Error Code:' . $this->errno(). 'Error Message:' . $this->error());
				var_dump('Database SQL execute failed!');
			}
		} else {
			//��ȡ��ǰ���е�controller��action����
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
	 * ��ȡ���ݿ����������Ϣ
	 * 
	 * @access public
	 * @return string
	 */
	public function error() {
		
		$info = $this->db_link->errorInfo();

		return $info[2];		
	}
	
	/**
	 * ��ȡ���ݿ������Ϣ����
	 * 
	 * @access public
	 * @return int
	 */
	public function errno() {
		
		return $this->db_link->errorCode();
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

		$myrow 	= $result->fetch(PDO::FETCH_ASSOC);
		$result = null;

		return $myrow;
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
		
		$myrow 	= $result->fetchAll(PDO::FETCH_ASSOC);
		$result = null;
		
		return $myrow;
	}
	
	/**
	 * ��ȡinsert_id
	 * 
	 * @access public
	 * @return int
	 */
	public function insert_id(){
		
		return $this->db_link->lastInsertId();
	}
	

	
	/**
	 * ����ģʽ
	 * 
	 * @access public
	 * @param array $params ���ݿ����Ӳ���,�����ݿ��������,�û���,�����
	 * @return object
	 */
	public static function getInstance($params) {		
		
		if (!self::$instance) {			
			self::$instance = new self($params);
		}
		
		return self::$instance;
	}
}
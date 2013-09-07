<?php

class Model
{
	
	/**
	 * ���ݱ���
	 * 
	 * @var string
	 */
	protected $table_name;
	
	/**
	 * ���ݱ��ֶ���Ϣ
	 * 
	 * @var array
	 */
	protected $table_field;
	
	/**
	 * ���ݱ��������Ϣ
	 * 
	 * @var string
	 */
	protected $primary_key;
	
	/**
	 * ���ݿ����ӵ�ʵ��������
	 * 
	 * @var $object
	 */
	protected $db;
	
	/**
	 * SQL������������ڴ��SQL��䣬ΪSQL�����װ�����ṩSQL���Ƭ�εĴ�ſռ䡣
	 * 
	 * @var array
	 */
	protected $_parts;
	
	/**
	 * ��ѯ������ʱ�������
	 * 
	 * @var object
	 */
	protected $myrow;

	/**
	 * ���캯��
	 * 
	 * ���ڳ�ʼ���������л�������Ի����������и�ֵ
	 * @access public
	 * @return boolean
	 */
	public function __construct($table_name) {		
	
		//�������ݿ������ļ�.
		$params = load_config('config');    //���ù��ߺ���
		
		//����,��������ļ�����
		if (!is_array($params)) {			
			var_dump('Contents of the file: config.inc.php is not correct! It must be an array.');
		}		
		
		//�Բ�������trim()���ݴ���
		$params['host'] 	= trim($params['host']);
		$params['username'] = trim($params['username']);
		$params['password'] = trim($params['password']);
		$params['dbname'] 	= trim($params['dbname']);
		
		//����Ĭ�ϲ�����Ĭ�ϱ���Ϊ:utf-8
		$params['charset'] 	= ($params['charset']) ? trim($params['charset']) : 'utf8';
		
		//�ù���ģʽʵ�������ݿ�������
		$this->db 			= $this->factory($params);
		
		//�����ݿ���û��������뼰ʱ���ڴ���ע������߳���ȫ��
		unset($params['username']);
		unset($params['password']);
		
		$this->table_name = $table_name;
		
		return true;		
	}
	
	
	/**
	 * ����ģʽʵ�������ݿ���������
	 * 
	 * ʵ�ֲ�ͬ���ݿ�������ʵ����,���������û��driver�����ݿ����ͣ�,Ĭ��Ϊmysqli������
	 * @access public
	 * @param array $params ���ݿ�������Ϣ�����ݿ�����Ӳ�����
	 * @return object
	 */
	public static function factory($params) {
		
		if ($params['driver'] == 'mysqli')
			$link_id = db_mysqli::getInstance($params);
		elseif ($params['driver'=='mysql'])
			$link_id = db_mysql::getInstance($params);
		
		return $link_id;
	}
	
		/**
	 * ��һ���֣���ȡ���ݱ�����ݱ������������ֶ���Ϣ�����й���Ϣ
	 */
	
	
	/**
	 * ��ȡ��ǰmodel����Ӧ�����ݱ������
	 * 
	 * ע:�����ݱ���ǰ׺($prefix)ʱ�����Զ��������ݱ�ǰ׺��
	 * @access protected
	 * @return string	���ݱ���
	 */
	protected function get_table_name() {

		//��$this->table_name������ʱ
		if (!$this->table_name) {
			//��ȡ��ǰmodel������
			$model_id = substr(strtolower(get_class($this)), 0, -5);
			//�������ݱ���������ǰ׺ʱ������ǰ׺
			$this->table_name = !empty($this->prefix) ? $this->prefix . $model_id : $model_id;
		}
		
		return $this->table_name;
	}
	
	/**
	 * ��ȡ���ݱ��ֶ���Ϣ
	 * 
	 * �����������ֶ���Ϣ,������������Ϊ����
	 * @access protected
	 * @return array	�ֶ���Ϣ
	 */
	protected function get_table_info() {
		
		//��ȡ���ݱ���
		$this->get_table_name();
		
		//��ѯ���ݱ��ֶ���Ϣ
		$sql="SHOW FIELDS FROM {$this->table_name}";
		
		return $this->db->get_array($sql);
	}
	
	/**
	 * ��ȡ���ݱ�����
	 * 
	 * @access protected
	 * @return string	���ݱ�����
	 */
	protected function get_primary_key() {
		
		//��$this->primary_key����Ϊ��ʱ
		if (!$this->primary_key) {			
			//���ػ����ļ�������ʱ,�򴴽������ļ�
			if (!$this->load_cache()) {
				$this->create_cache();
			}			
		}
		
		return $this->primary_key;
	}
	
	/**
	 * ��ȡ���ݱ��ֶ���Ϣ
	 * 
	 * @access protected
	 * @return array	���ݱ��ֶ���Ϣ
	 */
	protected function get_table_fields() {
		
		//��$this->table_field����Ϊ��ʱ,�����model�����ļ�
		if (!$this->table_field) {
			//����model�����ļ�ʧ�ܻ򻺴��ļ�������ʱ,����model�����ļ�
			if (!$this->load_cache()) {
				$this->create_cache();				
			}			
		}
		
		return $this->table_field;
	}
	
	
	/**
	 * �ڶ����֣�SQL��� ��װ��from, where, order by, limit, left join , group by, having, orwhere�ȣ�
	 */
	
	
	/**
	 * ��װSQL����е�FROM���
	 * 
	 * ���ڴ��� SELECT fields FROM table֮���SQL��䲿��
	 * @access public
	 * @param mixed $table_name  ��Ҫ��ѯ�����ݱ���������֧������
	 * @param mixed $columns	   ��Ҫ��ѯ�����ݱ��ֶΣ�����֧�����飬Ĭ��Ϊnull, �����ݱ�ȫ���ֶ�
	 * @return $this
	 * 
	 * @example
	 * $model = new DemoModel();
	 * 
	 * ��һ��	 
	 * $model->from('���ݱ���', array('id', 'name', 'age'));
	 * 
	 * ������
	 * $model->from('���ݱ���'); //���ڶ�����Ϊ��ʱ��Ĭ��Ϊȫ���ֶ�
	 * 
	 * ����:
	 * $model->from(array('p'=>'product', 'i'=>'info'), array('p.id', 'p.name', 'i.value'));
	 * 
	 * ���ģ�
	 * $model->from('list', array('total_num'=>'count(id)')); //�ڶ�����֧��ʹ��SQL������Ŀǰ֧�֣�count(),sum(),avg(),max(),min(), distinct()��
	 * 
	 * ���壺
	 * $model->from();
	 * 
	 */
	public function from($table_name = null, $fields = null) {
		
		//��������
		if (!$table_name) {
			$this->get_table_name();
			$table_name = $this->table_name;
		}
		
		//�����ݱ����Ƶķ���
		if (is_array($table_name)) {
			
			$option_array = array();
			foreach ($table_name as $key=>$value) {
				//�������ݱ�ǰ׺ʱ
				if (!empty($this->prefix)) {
					$option_array[] = is_int($key) ? ' ' .$this->prefix . trim($value) : ' ' . $this->prefix . trim($value) . ' AS ' . $key;  
				} else {
					$option_array[] = is_int($key) ? ' ' . trim($value) : ' ' . trim($value) . ' AS ' . $key;
				}
			}
			$table_str = implode(',', $option_array);
			//��ղ���Ҫ���ڴ�ռ��
			unset($option_array);
						
		} else {
			
			$table_str = (!empty($this->prefix)) ? $this->prefix . trim($table_name) : trim($table_name); 
		}
		
		//�����ݱ��ֶεķ���
		$item_str = $this->_parse_fields($fields);		
		
		//��װSQL�е�FROMƬ��
		$this->_parts['from'] = 'SELECT ' . $item_str . ' FROM ' . $table_str;
		
		return $this;
	}
	
	/**
	 * �������ݱ��ֶ���Ϣ
	 * 
	 * @access 	protected
	 * @param	array	$fields	���ݱ��ֶ���Ϣ.������Ϊ����
	 * @return 	string
	 */
	protected static function _parse_fields($fields = null) {
		
		if (is_null($fields)) {
			return '*';
		}
		
		if(is_array($fields)) {
			$fields_array = array();
			foreach($fields as $key=>$value) {
				$fields_array[] = is_int($key) ? $value : $value . ' AS ' . $key; 
			}
			$fields_str = implode(',', $fields_array);
			//��ղ���Ҫ���ڴ�ռ��
			unset($fields_array);
		} else {
			$fields_str = $fields;
		}
		
		return $fields_str;
	}
	
	/**
	 * ��װSQL����WHERE���
	 * 
	 * ���ڴ��� WHERE id=3721 ��������SQL��䲿��
	 * @access public
	 * @param string $where WHERE������
	 * @param string $value ���ݲ�����һ��Ϊ�ַ����ַ���
	 * @return $this
	 * 
	 * @example
	 * $model = new DemoModel();
	 * 
	 * ��һ��
	 * $model->where('id=23');
	 * 
	 * ����:
	 * $model->where('name=?', 'doitphp');
	 * 
	 * ����:
	 * $model->where(array('class=3', 'age>21', 'no=10057'));
	 * 
	 * ���ģ�
	 * ����������Ҳ��������..
	 * $model->where('class=3')->where('age>21')->where('no=10057');
	 * 
	 * ����:
	 * $model->where(array('id>5', 'name=?'), 'tommy');
	 *  
	 * ע�������÷�������Ԫ����ֻ�������һ���ʺţ�?��,��(?)����ͬһ����ֵʱ�������п��Գ��������ʺ�
	 * ��(����name��group��Ϊtommy������):
	 * $model->where(array('name=?', 'group=?'), 'tommy');
	 * 	 
	 */
	public function where($where, $value = null) {
		
		return $this->_where($where, $value, true);
	}
	
	/**
	 * ��װSQL����ORWHERE���
	 * 
	 * ���ڴ��� ORWHERE id=2011 ��������SQL��䲿��
	 * @access public
	 * @param string $where WHERE������
	 * @param string $value ���ݲ�����һ��Ϊ�ַ����ַ���
	 * @return $this
	 * 
	 * @example
	 * ʹ�÷�����$this->where()����
	 */
	public function orwhere($where, $value = null) {
		
		return $this->_where($where, $value, false);
	}
	
	/**
	 * ��װSQL�����WHERE��ORWHERE���
	 * 
	 * ����������Ϊ����where()��orwhere()�ṩ"���"
	 * @access protected
	 * @param string $where SQL�е�WHERE����е�����.
	 * @param string $value ��ֵ�����ݱ�ĳ�ֶ�����Ӧ�����ݣ�ͨ����Ϊ�ַ������ַ���
	 * @param boolean $is_where ע:Ϊtrueʱ��Ϊwhere()�� ��֮ Ϊorwhere()
	 * @return $this
	 */
	protected function _where($where, $value = null, $is_where = true) {
		
		//��������
		if (!$where) {
			return false;
		}
		
		//��������������������Ϊ����ʱ
		if (is_array($where)) {			
			$where_array = array();
			foreach ($where as $string) {
				$where_array[] = trim($string);
			}
			
			$where = implode(' AND ', $where_array);
			unset($where_array);
		}
		
		//��$model->where('name=?', 'tommy');����ʱ
		if (!is_null($value)) {
			$value = $this->quote_into($value);
			$where = str_replace('?', $value, $where);
		}
		
		//����where��orwhere.
		if ($is_where == true) {
			$this->_parts['where'] 		= ($this->_parts['where']) ? $this->_parts['where'] . ' AND ' . $where : ' WHERE ' . $where;
		} else {
			$this->_parts['or_where'] 	= ($this->_parts['or_where']) ? $this->_parts['or_where'] . ' AND ' . $where : ' OR ' . $where;
		}
		
		return $this;
	}
	
	/**
	 * ��װSQL�������(ORDER BY)���
	 * 
	 * ���ڴ��� ORDER BY post_id ASC ����֮���SQL��䲿��
	 * @access public
	 * @param mixed $string ����������ע��������֧������
	 * @return $this
	 */
	public function order($string) {
		
		//��������
		if (!$string) {
			return false;
		}
		
		//������Ϊ����ʱ
		if (is_array($string)) {
			$order_array = array();
			foreach ($string as $lines) {
				$order_array[] = trim($lines);
			}
			$string = implode(',', $order_array);
			unset($order_array);			
		}
		
		$string = trim($string);		
		$this->_parts['order'] = ($this->_parts['order']) ? $this->_parts['order'] . ', ' . $string : ' ORDER BY ' . $string;
		
		return $this;		
	}
	
	/**
	 * ��װSQL���LIMIT���
	 * 
	 * limit(10,20)���ڴ���LIMIT 10, 20֮���SQL��䲿�� 
	 * @access public
	 * @param int $offset ��ʼid, ע:����Ϊ����
	 * @param int $count  ��ʾ������
	 * @return $this
	 */
	public function limit($offset, $count = null) {
		
		//��������
		$offset 	= (int)$offset;
		$count 		= (int)$count;
		
		$limit_str = !empty($count) ? $offset . ', ' . $count : $offset;
		$this->_parts['limit'] = ' LIMIT ' . $limit_str;
		
		return $this;
	}
	
	/**
	 * ��װSQL����LIMIT���
	 * 
	 * ע:��������$this->limit()�������࣬��������:���������ڷ�ҳ,������ͬ
	 * @access public
	 * @param int $page 	��ǰ��ҳ��
	 * @param int $count 	ÿҳ��ʾ����������
	 * @return $this
	 */
	public function page_limit($page, $count) {
		
		//��������
		$page		= (int)$page;
		$count	= (int)$count;

		$start_id = $count * ($page - 1);

		return $this->limit($start_id, $count);
	}
	
	/**
	 * ��װSQL�����LEFT JOIN���
	 * 
	 * jion('����2', '��ϵ���')�൱��SQL�����LEFT JOIN ��2 ON ��ϵSQL��䲿��
	 * @access public
	 * @param string $table_name	���ݱ�����ע��������֧�����飬��Ҫ�������ݱ��alias����
	 * @param string $where			join������ע����֧������
	 * @return $this
	 */
	public function join($table_name, $where) {
		
		//��������
		if (!$table_name || !$where) {
			return false;
		}
		
		//�������ݱ���
		if (is_array($table_name)) {									
			foreach ($table_name as $key=>$string) {																
				if (!empty($this->prefix)) {
					$table_name_str = is_int($key) ? $this->prefix . trim($string) : $this->prefix . trim($string) . ' AS ' . $key;
				} else {
					$table_name_str = is_int($key) ? trim($string) :  trim($string) .' AS ' . $key;	
				}
				//���ݴ���ֻ����һ������Ԫ��
				break;				
			}			
		} else {			
			$table_name_str = (!empty($this->prefix)) ? $this->prefix . trim($table_name) : trim($table_name);
		}
		
		//�����������
		$where = trim($where);		
		$this->_parts['join'] = ' LEFT JOIN ' . $table_name_str . ' ON ' . $where;
		
		return $this;
	}
	
	/**
	 * ��װSQL��GROUP BY���
	 * 
	 * ���ڴ���SQL�����GROUP BY��䲿��
	 * @access public
	 * @param mixed $group_name	��Ҫ������ֶζ���
	 * @return $this
	 */
	public function group($group_name) {
		
		//��������
		if (!$group_name) {
			return false;
		}
		
		if (is_array($group_name)) {			
			$group_array = array();
			foreach ($group_name as $lines) {
				$group_array[] = trim($lines);
			}
			$group_name = implode(',', $group_array);
			unset($group_array); 
		}

		$this->_parts['group'] = ($this->_parts['group']) ? $this->_parts['group'] . ', ' . $group_name : ' GROUP BY ' . $group_name;
		
		return $this;
	}
	
	/**
	 * ��װSQL��HAVING���
	 * 
	 * ���ڴ��� having id=2011 ��������SQL��䲿��
	 * @access pulbic
	 * @param string|array $where �������
	 * @param string $value	���ݱ�ĳ�ֶε�����ֵ
	 * @return $this
	 * 
	 * @example
	 * �÷���where()����
	 */
	public function having($where, $value = null) {
		
		return $this->_having($where, $value, true);
	}
	
	/**
	 * ��װSQL��ORHAVING���
	 * 
	 * ���ڴ���or having id=2011 ��������SQL��䲿��
	 * @access pulbic
	 * @param string|array $where �������
	 * @param string $value	���ݱ�ĳ�ֶε�����ֵ
	 * @return $this
	 * 
	 * @example
	 * �÷���where()����
	 */
	public function orhaving($where, $value = null) {
		
		return $this->_having($where, $value, false);
	}
	
	/**
	 * ��װSQL��HAVING,ORHAVING���
	 * 
	 * Ϊhaving()��orhaving()������ִ���ṩ'���'
	 * @access protected
	 * @param mixed $where �������
	 * @param string $value	���ݱ�ĳ�ֶε�����ֵ
	 * @param boolean $is_having ������Ϊtrueʱ������having()����Ϊfalseʱ����Ϊorhaving()
	 * @return $this
	 */
	protected function _having($where, $value = null, $is_having = true) {
		
		//��������
		if (!$where) {
			return false;
		}
		
		//��������������������Ϊ����ʱ
		if (is_array($where)) {						
			$where_array = array();
			foreach ($where as $string) {
				$where_array[] = trim($string);
			}
			
			$where = implode(' AND ', $where_array);
			unset($where_array);
		}
		
		//������$model->where('name=?', 'tommy');����ʱ
		if (!is_null($value)) {
			$value = $this->quote_into($value);
			$where = str_replace('?', $value, $where);
		}
		
		//����having() �� orhaving()
		if ($is_having == true) {
			$this->_parts['having'] 	= ($this->_parts['having']) ? $this->_parts['having'] . ' AND ' . $where : ' HAVING ' . $where;
		} else {
			$this->_parts['or_having'] 	= ($this->_parts['or_having']) ? $this->_parts['or_having'] . ' AND ' . $where : ' OR ' . $where;
		}
		
		return $this;
	}
	
	/**
	 * ִ��SQL����е�SELECT��ѯ���
	 * 
	 * ��װSQL��䲢��ɲ�ѯ�������ز�ѯ��������ؽ�������Ƕ��У�Ҳ�����ǵ���
	 * @access public
	 * @param boolean $all_data �Ƿ����Ϊ�������ݣ�Ĭ��Ϊtrue,���������ݣ���falseʱ�����Ϊ��������
	 * @return array
	 */
	public function select($all_data = true) {
		
		//������ѯ���ݱ�����
		if (!$this->_parts['from']) {
			return false;
		}
		
		//��װ������SQL��ѯ���
		$parts_name_array = array('from', 'join', 'where', 'or_where', 'group', 'having', 'or_having', 'order', 'limit');
		$sql_str = '';
		foreach ($parts_name_array as $part_name) {			
			if ($this->_parts[$part_name]) {
				$sql_str .= $this->_parts[$part_name];
				unset($this->_parts[$part_name]);
			}
		}
		
		return ($all_data == true) ? $this->db->get_array($sql_str) : $this->db->fetch_row($sql_str);
	}
	
	/**
	 * �ַ���ת�庯��
	 * 
	 * SQL���ָ�ȫ����,�����ַ�ת��
	 * @access public
	 * @param mixed $value ��Ҫת����ַ����ַ���,ע������֧������
	 * @return string|string
	 */
	public static function quote_into($value) {
		
		//�����Ƿ�Ϊ����
		if (is_array($value)) {			
			foreach ($value as $key=>$string) {
				$value[$key] = self::quote_into($string);
			}
		} else {
			//������Ϊ�ַ������ַ�ʱ
			if (is_string($value)) {
				$value = '\'' . addslashes($value) . '\'';
			}
		}
		
		return $value;
	}
	
	/*
	 * ��ȡ���ݵ�������
	 * 
	 * ��ȡĳ���ݱ�����һ�����������ݵ�������,��ҳ������
	 * @access public
	 * @param string $table_name	��Ҫ��ѯ�����ݱ���
	 * @param string $field_name	��Ҫ��ѯ�ֶ�����
	 * @param string $where			��ѯ����
	 * @param string $value			��ֵ�����ݱ�ĳ�ֶ�����Ӧ�����ݣ�ͨ����Ϊ�ַ������ַ���
	 * @return integer
	 */
	public function count($table_name, $field_name = null, $where = null, $value = null) {
		
		//�����ж�
		if (!$table_name) {
			return false;
		}
		
		if (!$field_name) {
			$this->get_primary_key();
			$field_name = $this->primary_key;
		}
		
		$data = (!is_null($where)) ? $this->from($table_name, array('total_num'=>'count(' . $field_name . ')'))->where($where, $value)->select(false) : $this->from($table_name, array('total_num'=>'count(' . $field_name . ')'))->select(false);
		
		return $data['total_num'];
	}
	
	/**
	 * �������֣������ݱ�Ĳ�ѯ�����ģ�д�룬ɾ��������ע���˺�����Ϊ���������ݲ�����������������
	 */
	
	
	/**
	 * �����ݱ��������ѯ
	 * 
	 * ������������ȡĳ��������һ����Ϣ,���������������á�Ĭ������Ϊ���ݱ����������
	 * ������ݱ�û��������������model�ж���
	 * @access public
	 * @param int|string|array $id ��Ҫ��ѯ������ֵ.ע��������֧�����飬������Ϊ����ʱ�����Բ�ѯ��������
	 * @param array	$fields	�������ݵ���Ч�ֶ�(���ݱ��ֶ�)
	 * @return string	��Ҫ��ѯ������Ϣ�����л���У�
	 * 
	 * @example
	 * 
	 * ʵ����model
	 * $model = new DemoModel();
	 * 
	 * $model->find(1024);
	 * 
	 * �Զ���model������
	 * $model->primary_key = 'memeber_name'; //�������Ͳ���int��Ҳ������varchar,char�����,������������������Ϊvarchar,charʱ������Ҫʹ��$this->quote_into()����ת�塣
	 * 
	 * $name = $this->quote_into('tommy');
	 * $model->find($name);
	 * 
	 */
	public function find($id, $fields = null) {
		
		//��������
		if (!$id) {
			return false;
		}
		
		//��ȡ���������ݱ���
		$this->get_table_name();
		$this->get_primary_key();
		
		//������ѯ���ֶ���Ϣ		
		$fields_str = $this->_parse_fields($fields);
		
		$sql_str = 'SELECT ' . $fields_str .' FROM ' . $this->table_name . ' WHERE ' . $this->primary_key;
		
		if (is_array($id)) {			
			$sql_str .= ' IN (\'' . implode('\',\'', $id) . '\')';
			$myrow = $this->db->get_array($sql_str);			
		} else{			
			$sql_str .= '=\'' . trim($id) . '\'';
			$myrow = $this->db->fetch_row($sql_str);
		}
		
		return $myrow;
	}
	
	/**
	 * ��ȡ���ݱ��ȫ��������Ϣ
	 * 
	 * ������Ϊ�������򣬻�ȡ���ݱ�ȫ��������Ϣ. ע:������ݱ��������ϴ�ʱ�����ô˺������������ݱ�����������������ݿ�������ڴ����,����������崻�
	 * @access public
	 * @param array		$fields	���ص����ݱ��ֶ�,Ĭ��Ϊȫ��.��SELECT * FROM table_name
	 * @param  boolean	$order_asc��������,��ΪtrueʱΪASC,ΪfalseʱΪDESC, Ĭ��ΪASC
	 * @param integer	$offset	limit����ID
	 * @param integer	$count	��ʾ������
	 * @return array	���ݱ�������Ϣ
	 */
	public function findAll($fields = null, $order_asc = true, $offset = null, $count = null) {
		
		//��ȡ���������ݱ���
		$this->get_table_name();
		$this->get_primary_key();
		
		//������ѯ���ֶ���Ϣ		
		$fields_str = $this->_parse_fields($fields);
		
		$sql_str  = 'SELECT ' . $fields_str . ' FROM ' . $this->table_name . ' ORDER BY ' . $this->primary_key . (($order_asc == true) ? ' ASC' : ' DESC');
		if (!is_null($offset)) {
			$this->_parts['limit'] = '';
			$this->limit($offset, $count);
			$sql_str .= $this->_parts['limit'];
			unset($this->_parts['limit']);
		}
		
		return $this->db->get_array($sql_str);
	}
	
	/**
	 * ��ѯ���ݱ�������
	 * 
	 * ����һ����ѯ��������ȡһ�����ݣ���������Ϊ�����ͣ�����Ϊ���ݱ��ֶ���
	 * @access public
	 * @param mixed 	$where ��ѯ����
	 * @param sring  	$value ��ֵ
	 * @param array		$fields	�������ݵ����ݱ��ֶ�,Ĭ��Ϊȫ���ֶ�.ע��������Ϊ����
	 * @return array 	��Ҫ��ѯ�����ݱ�����
	 * 
	 * @example
	 * 
	 * ��һ��
	 * $data = $model->getOne('name=?', 'tommy');
	 * 
	 * ������
	 * $data = $model->getOne(array('age>23', 'class=4'));
	 * 
	 * ������
	 * $data = $model->getOne('name=?', 'tommy', array('name', 'age', 'addr'));
	 */
	public function getOne($where, $value = null, $fields = null) {
		
		//��������
		if (!$where) {
			return false;
		}
		
		//��ȡ���ݱ���
		$this->get_table_name();
		
		//������ѯ���ֶ���Ϣ		
		$fields_str = $this->_parse_fields($fields);
		
		//�����ѯ��SQL���
		$this->_parts['where'] = '';
		$this->where($where, $value);
		$where_str = $this->_parts['where'];
		unset($this->_parts['where']);
		
		$sql_str = 'SELECT ' . $fields_str . ' FROM ' . $this->table_name . $where_str;
		
		return $this->db->fetch_row($sql_str);
	}
	
	/**
	 * ��ѯ���ݱ��������
	 * 
	 * ����һ����ѯ��������ȡ�������ݡ�����֧����������
	 * @access public
	 * @param mixed	$where ��ѯ����
	 * @param sring	$value ��ֵ
	 * @param array $fields	�������ݵ����ݱ��ֶ�.Ĭ��Ϊȫ���ֶ�.ע:������Ϊ����
	 * @param mixed $order ��������
	 * @param integer	$offset	limit����ID
	 * @param integer	$count	��ʾ������ 
	 * @return array 
	 */
	public function getAll($where, $value=null, $fields = null, $order = null, $offset = null, $count = null) {
		
		//��������
		if (!$where) {
			return false;
		}
		
		//��ȡ���ݱ���
		$this->get_table_name();
		
		//������ѯ���ֶ���Ϣ		
		$fields_str = $this->_parse_fields($fields);
		
		$sql_str = 'SELECT ' . $fields_str . ' FROM '.$this->table_name;
		
		//�����ѯ��SQL���
		$this->_parts['where'] = '';
		$this->where($where, $value);
		$sql_str .= $this->_parts['where'];
		unset($this->_parts['where']);
		
		//���������SQL���
		if (!is_null($order)) {
			$this->_parts['order'] = '';
			$this->order($order);
			$sql_str .= $this->_parts['order'];
			unset($this->_parts['order']);
		}
		
		//����limit���
		if (!is_null($offset)) {
			$this->_parts['limit'] = '';
			$this->limit($offset, $count);
			$sql_str .= $this->_parts['limit'];
			unset($this->_parts['limit']);
		}
		
		return $this->db->get_array($sql_str);
	}
	
	/**
	 * ���ݱ�д�����
	 * 
	 * ��ǰmodel��Ӧ�����ݱ��������
	 * @access public
	 * @param array $data ��Ҫд����������ݡ�ע�����ݱ���Ϊ����
	 * @return boolean
	 * 
	 * @example
	 * 
	 * $data = array('name'=>'tommy', 'age'=>23, 'addr'=>'ɽ��'); //ע������ļ�ֵ�����ݱ���ֶ���
	 * 
	 * $model->insert($data);
	 */
	public function insert($data) {
		
		//��������
		if (!is_array($data) || !$data) {
			return false;
		}
		
		//��ȡ���ݱ������ֶ���Ϣ
		$this->get_table_name();
		$this->get_table_fields();

		//�������ݱ��ֶ������ݵĶ�Ӧ��ϵ
		$field_array 	= array();
		$content_array 	= array();
		
		foreach ($data as $key=>$value) {
						
			if (in_array($key, $this->table_field)) {
				$field_array[] 	= trim($key);
				$content_array[]= '\'' . addslashes(trim($value)) . '\'';
			}
		}
		
		$field_str 		= implode(',', $field_array);
		$content_str	= implode(',', $content_array);
		
		//��ղ���Ҫ���ڴ�ռ��
		unset($field_array);
		unset($content_array);
		
		$sql_str = 'INSERT INTO ' . $this->table_name . ' (' . $field_str . ') VALUES (' . $content_str . ')';
				
		return $this->db->query($sql_str);
	}
	
	/**
	 * ���ݱ���Ĳ���
	 * 
	 * ���ĵ�ǰmodel����Ӧ�����ݱ����������
	 * @access public
	 * @param array 	$data ��Ҫ���ĵ���������
	 * @param mixed		$where ����������Ҫ���������
	 * @param string	$$params ��ֵ����������ĵ������Ľ�һ������
	 * @return boolean
	 */
	public function update($data, $where, $params = null) {
		
		//��������
		if (!is_array($data) || !$data || !$where) {
			return false;
		}
		
		//��ȡ���ݱ������ֶ���Ϣ
		$this->get_table_name();
		$this->get_table_fields();
		
		$content_array = array();
		foreach ($data as $key=>$value) {
			if (in_array($key, $this->table_field)) {
				$content_array[] = $key . ' = \'' . addslashes(trim($value)) . '\'';
			}
		}
		$content_str = implode(',', $content_array);
		unset($content_array);
		
		//��װSQL���
		$sql_str = 'UPDATE ' . $this->table_name . ' SET ' . $content_str;
		
		//������ѯSQL���Ĵ���
		$this->_parts['where'] = '';
		$this->where($where, $params);
		$sql_str .= $this->_parts['where'];
		unset($this->_parts['where']);
		
		return $this->db->query($sql_str);
	}
	
	/**
	 * ���ݱ�ɾ������
	 * 
	 * �ӵ�ǰmodel����Ӧ�����ݱ���ɾ������һ����ѯ��������������
	 * @access public
	 * @param  mixed 	$where ��Ҫ���������
	 * @param  sring	$value ��ֵ�������������Ľ�һ������
	 * @return boolean
	 */
	public function delete($where, $value = null) {
		
		//��������
		if (!$where) {
			return false;
		}
		
		//��ȡ���ݱ������ֶ���Ϣ
		$this->get_table_name();
		
		$sql_str = 'DELETE FROM ' . $this->table_name;
		
		//����SQL��������ѯ���
		$this->_parts['where'] = '';
		$this->where($where, $value);
		$sql_str .= $this->_parts['where'];
		unset($this->_parts['where']);
		
		return $this->db->query($sql_str);
	}
	
	
	/**
	 * ���Ĳ���:�����ݱ���в�ѯ,����,��� �Ȳ���. ע���˷���������������Ϊ�����ͣ���������
	 */
	
	
	/**
	 * �½�һ�����ݣ������͵�
	 * 
	 * @access public
	 * @return object
	 */
	public function createRow() {
		
		//��ʼ��$this->myrow.
		$this->myrow = array();
		$this->myrow = (object)$this->myrow;
		
		return $this;
	}
	
	/**
	 * ���ݱ��ѯ����
	 *  
	 * ���ݲ�ѯ������ȡһ�����ݣ������͵�
	 * @access public
	 * @param mixed		$where ��ѯ����
	 * @param string 	$value ��ֵ����ѯ�����Ľ�һ������
	 * @return $this	
	 */
	public function fetchRow($where, $value = null) {
		
		//��������
		if (!$where) {
			return false;
		}
		
		$this->myrow = (object)$this->getOne($where, $value);
		return $this;
	}
	
	/**
	 * ���ݱ����ݱ������
	 * 
	 * �����������ݱ��浽���ݱ��У��Ӷ���ɶ����ݵĸ��Ļ���Ӳ���
	 * @access public
	 * @return boolean
	 */
	public function save() {
		
		//��$this->myrow��������Ϊ�Ƕ�����ʱ
		if (!is_object($this->myrow)) {
			return false;
		}
		
		//��ȡ���ݱ�����
		$this->get_primary_key();
		
		//����ת��,ת�ɿɲ�����������
		$myrow 		= (array)$this->myrow;		
		$key_array 	= array_keys($myrow);
		
		//��$key_array�д������ݱ�����ʱ��˵�����ڵ�$this->myrow�����������ڲ�ѯ�����������൱�����ݸ���
		if (in_array($this->primary_key, $key_array)) {			
			$where = '' . $this->primary_key . '=' . $this->quote_into($myrow[$this->primary_key]);
			$this->update($myrow, $where);			
		} else {
			$this->insert($myrow);
		}

		return true;
	}
	
	/**
	 * ���岿�֣���������������ִ��SQL����ѯ������
	 */
	
	
	/**
	 * ִ��SQL��ѯ���
	 * 
	 * ִ��һ��SQL��䲢��ȡִ�к��ȫ������
	 * @access public
	 * @param string $sql SQL���
	 * @param boolean $all_rows �Ƿ���ʾȫ�����ݿ��أ���Ϊtrueʱ����ʾȫ�����ݣ�Ϊfalseʱ����ʾһ�����ݣ�Ĭ��Ϊtrue
	 * @return array 
	 */
	public function execute($sql, $all_data = true) {
		
		//��������
		if (!$sql) {
			return false;
		}
		
		return ($all_data == true) ? $this->db->get_array($sql) : $this->db->fetch_row($sql);
	}
	
	/**
	 * ִ��SQL������
	 * 
	 * ����ִ��һ��SQL��䣬��ɶ����ݱ���Ϣ�ĸ��ģ���ӣ�ɾ���Ȳ���.ע:��SQL��ѯ���Ӧ��ʹ��execute(),��Ϊquery()����ֵΪboolean.
	 * @access public
	 * @param string $sql ��Ҫִ�е�SQL���
	 * @return boolean
	 */
	public function query($sql) {
		
		//��������
		if (!$sql) {
			return false;
		}
		
		return $this->db->query($sql);
	}
	
	/**
	 * ��ȡinsert_id����
	 * 
	 * ��ȡ���ݱ��ϴ�д�����ʱ��insert_id
	 * @access public
	 * @return int	insert_id
	 */
	public function get_insert_id() {
		
		return $this->db->insert_id();
	}
	
	/**
	 * �Զ���������
	 *  
	 * ��������ʱ�Զ��������������Ϊprotected��private�ı����ĸ�ֵ ��
	 * 
	 * @access public
	 * @param string $name ������
	 * @param string $value ����ֵ
	 * @return void
	 */
	 public function __set($name, $value) {
	 	
	 	if (is_object($this->myrow)) {	 		
	 		$this->myrow->$name = addslashes($value);	 		
	 	} else {	 		
	 		//����model�����ݱ��������ݱ��������Զ���
	 		if (in_array($name, array('table_name', 'primary_key'))) {
	 			$this->$name = addslashes($value);
	 		}
	 	}
	 	
	 	return true; 	
	 }
	 
	 /**
	  * ������ʵ��������
	  * 
	  * ֱ�ӵ��ú���,�������,ͨ�����������װ��SQL��ѯ���
	  * @access public
	  * @return string
	  */
	 public function __toString() {
	 	
	 	if ($this->_parts) {
	 		
	 		$parts_name_array = array('from', 'join', 'where', 'or_where', 'group', 'having', 'or_having', 'order', 'limit');	 		
			
	 		$sql_str = '';			
			foreach ($parts_name_array as $part_name) {			
				if ($this->_parts[$part_name]) {
					$sql_str .= $this->_parts[$part_name];	
					unset($this->_parts[$part_name]);				
				}
			}
			
			return (string)$sql_str;
	 	}
	 	
	 	return (string)'This is '.get_class($this).' Class!';
	 }
	
	/**
	 * ��������
	 * 
	 * ������������н���������"��ɨս��"���磺�����Ч���ڴ�ռ�õ�
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		//��ղ���Ҫ���ڴ�ռ��		
		$unset_array = array($this->_parts, $this->myrow);
		foreach ($unset_array as $name) {
			//����������ʱ
			if (isset($name)) {
				unset($name);
			}			
		}
	}
}
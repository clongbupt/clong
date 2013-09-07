<?php

class Model
{
	
	/**
	 * 数据表名
	 * 
	 * @var string
	 */
	protected $table_name;
	
	/**
	 * 数据表字段信息
	 * 
	 * @var array
	 */
	protected $table_field;
	
	/**
	 * 数据表的主键信息
	 * 
	 * @var string
	 */
	protected $primary_key;
	
	/**
	 * 数据库连接的实例化对象
	 * 
	 * @var $object
	 */
	protected $db;
	
	/**
	 * SQL语句容器，用于存放SQL语句，为SQL语句组装函数提供SQL语句片段的存放空间。
	 * 
	 * @var array
	 */
	protected $_parts;
	
	/**
	 * 查询数据临时存放容器
	 * 
	 * @var object
	 */
	protected $myrow;

	/**
	 * 构造函数
	 * 
	 * 用于初始化程序运行环境，或对基本变量进行赋值
	 * @access public
	 * @return boolean
	 */
	public function __construct($table_name) {		
	
		//加载数据库配置文件.
		$params = load_config('config');    //调用工具函数
		
		//分析,检测配置文件内容
		if (!is_array($params)) {			
			var_dump('Contents of the file: config.inc.php is not correct! It must be an array.');
		}		
		
		//对参数进行trim()数据处理
		$params['host'] 	= trim($params['host']);
		$params['username'] = trim($params['username']);
		$params['password'] = trim($params['password']);
		$params['dbname'] 	= trim($params['dbname']);
		
		//分析默认参数，默认编码为:utf-8
		$params['charset'] 	= ($params['charset']) ? trim($params['charset']) : 'utf8';
		
		//用工厂模式实例化数据库驱动类
		$this->db 			= $this->factory($params);
		
		//将数据库的用户名及密码及时从内存中注销，提高程序安全性
		unset($params['username']);
		unset($params['password']);
		
		$this->table_name = $table_name;
		
		return true;		
	}
	
	
	/**
	 * 工厂模式实例化数据库驱动操作
	 * 
	 * 实现不同数据库驱动的实例化,如果参数中没有driver（数据库类型）,默认为mysqli驱动。
	 * @access public
	 * @param array $params 数据库配置信息（数据库的连接参数）
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
	 * 第一部分：获取数据表的数据表名，主键，字段信息，等有关信息
	 */
	
	
	/**
	 * 获取当前model所对应的数据表的名称
	 * 
	 * 注:若数据表有前缀($prefix)时，将自动加上数据表前缀。
	 * @access protected
	 * @return string	数据表名
	 */
	protected function get_table_name() {

		//当$this->table_name不存在时
		if (!$this->table_name) {
			//获取当前model的类名
			$model_id = substr(strtolower(get_class($this)), 0, -5);
			//分析数据表名，当有前缀时，加上前缀
			$this->table_name = !empty($this->prefix) ? $this->prefix . $model_id : $model_id;
		}
		
		return $this->table_name;
	}
	
	/**
	 * 获取数据表字段信息
	 * 
	 * 主键及所有字段信息,返回数据类型为数组
	 * @access protected
	 * @return array	字段信息
	 */
	protected function get_table_info() {
		
		//获取数据表名
		$this->get_table_name();
		
		//查询数据表字段信息
		$sql="SHOW FIELDS FROM {$this->table_name}";
		
		return $this->db->get_array($sql);
	}
	
	/**
	 * 获取数据表主键
	 * 
	 * @access protected
	 * @return string	数据表主键
	 */
	protected function get_primary_key() {
		
		//当$this->primary_key内容为空时
		if (!$this->primary_key) {			
			//加载缓存文件不存在时,则创建缓存文件
			if (!$this->load_cache()) {
				$this->create_cache();
			}			
		}
		
		return $this->primary_key;
	}
	
	/**
	 * 获取数据表字段信息
	 * 
	 * @access protected
	 * @return array	数据表字段信息
	 */
	protected function get_table_fields() {
		
		//当$this->table_field内容为空时,则加载model缓存文件
		if (!$this->table_field) {
			//加载model缓存文件失败或缓存文件不存在时,创建model缓存文件
			if (!$this->load_cache()) {
				$this->create_cache();				
			}			
		}
		
		return $this->table_field;
	}
	
	
	/**
	 * 第二部分：SQL语句 组装（from, where, order by, limit, left join , group by, having, orwhere等）
	 */
	
	
	/**
	 * 组装SQL语句中的FROM语句
	 * 
	 * 用于处理 SELECT fields FROM table之类的SQL语句部分
	 * @access public
	 * @param mixed $table_name  所要查询的数据表名，参数支持数组
	 * @param mixed $columns	   所要查询的数据表字段，参数支持数组，默认为null, 即数据表全部字段
	 * @return $this
	 * 
	 * @example
	 * $model = new DemoModel();
	 * 
	 * 法一：	 
	 * $model->from('数据表名', array('id', 'name', 'age'));
	 * 
	 * 法二：
	 * $model->from('数据表名'); //当第二参数为空时，默认为全部字段
	 * 
	 * 法三:
	 * $model->from(array('p'=>'product', 'i'=>'info'), array('p.id', 'p.name', 'i.value'));
	 * 
	 * 法四：
	 * $model->from('list', array('total_num'=>'count(id)')); //第二参数支持使用SQL函数，目前支持，count(),sum(),avg(),max(),min(), distinct()等
	 * 
	 * 法五：
	 * $model->from();
	 * 
	 */
	public function from($table_name = null, $fields = null) {
		
		//参数分析
		if (!$table_name) {
			$this->get_table_name();
			$table_name = $this->table_name;
		}
		
		//对数据表名称的分析
		if (is_array($table_name)) {
			
			$option_array = array();
			foreach ($table_name as $key=>$value) {
				//当有数据表前缀时
				if (!empty($this->prefix)) {
					$option_array[] = is_int($key) ? ' ' .$this->prefix . trim($value) : ' ' . $this->prefix . trim($value) . ' AS ' . $key;  
				} else {
					$option_array[] = is_int($key) ? ' ' . trim($value) : ' ' . trim($value) . ' AS ' . $key;
				}
			}
			$table_str = implode(',', $option_array);
			//清空不必要的内存占用
			unset($option_array);
						
		} else {
			
			$table_str = (!empty($this->prefix)) ? $this->prefix . trim($table_name) : trim($table_name); 
		}
		
		//对数据表字段的分析
		$item_str = $this->_parse_fields($fields);		
		
		//组装SQL中的FROM片段
		$this->_parts['from'] = 'SELECT ' . $item_str . ' FROM ' . $table_str;
		
		return $this;
	}
	
	/**
	 * 分析数据表字段信息
	 * 
	 * @access 	protected
	 * @param	array	$fields	数据表字段信息.本参数为数组
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
			//清空不必要的内存占用
			unset($fields_array);
		} else {
			$fields_str = $fields;
		}
		
		return $fields_str;
	}
	
	/**
	 * 组装SQL语句的WHERE语句
	 * 
	 * 用于处理 WHERE id=3721 诸如此类的SQL语句部分
	 * @access public
	 * @param string $where WHERE的条件
	 * @param string $value 数据参数，一般为字符或字符串
	 * @return $this
	 * 
	 * @example
	 * $model = new DemoModel();
	 * 
	 * 法一：
	 * $model->where('id=23');
	 * 
	 * 法二:
	 * $model->where('name=?', 'doitphp');
	 * 
	 * 法三:
	 * $model->where(array('class=3', 'age>21', 'no=10057'));
	 * 
	 * 法四：
	 * 法三的例子也可以这样..
	 * $model->where('class=3')->where('age>21')->where('no=10057');
	 * 
	 * 法五:
	 * $model->where(array('id>5', 'name=?'), 'tommy');
	 *  
	 * 注：此种用法，数组元素中只允许出现一个问号（?）,若(?)代表同一个数值时，数组中可以出现两个问号
	 * 如(查找name和group均为tommy的数据):
	 * $model->where(array('name=?', 'group=?'), 'tommy');
	 * 	 
	 */
	public function where($where, $value = null) {
		
		return $this->_where($where, $value, true);
	}
	
	/**
	 * 组装SQL语句的ORWHERE语句
	 * 
	 * 用于处理 ORWHERE id=2011 诸如此类的SQL语句部分
	 * @access public
	 * @param string $where WHERE的条件
	 * @param string $value 数据参数，一般为字符或字符串
	 * @return $this
	 * 
	 * @example
	 * 使用方法与$this->where()类似
	 */
	public function orwhere($where, $value = null) {
		
		return $this->_where($where, $value, false);
	}
	
	/**
	 * 组装SQL语句中WHERE及ORWHERE语句
	 * 
	 * 本方法用来为方法where()及orwhere()提供"配件"
	 * @access protected
	 * @param string $where SQL中的WHERE语句中的条件.
	 * @param string $value 数值（数据表某字段所对应的数据，通常都为字符串或字符）
	 * @param boolean $is_where 注:为true时是为where()， 反之 为orwhere()
	 * @return $this
	 */
	protected function _where($where, $value = null, $is_where = true) {
		
		//参数分析
		if (!$where) {
			return false;
		}
		
		//分析参数条件，当参数为数组时
		if (is_array($where)) {			
			$where_array = array();
			foreach ($where as $string) {
				$where_array[] = trim($string);
			}
			
			$where = implode(' AND ', $where_array);
			unset($where_array);
		}
		
		//当$model->where('name=?', 'tommy');操作时
		if (!is_null($value)) {
			$value = $this->quote_into($value);
			$where = str_replace('?', $value, $where);
		}
		
		//处理where或orwhere.
		if ($is_where == true) {
			$this->_parts['where'] 		= ($this->_parts['where']) ? $this->_parts['where'] . ' AND ' . $where : ' WHERE ' . $where;
		} else {
			$this->_parts['or_where'] 	= ($this->_parts['or_where']) ? $this->_parts['or_where'] . ' AND ' . $where : ' OR ' . $where;
		}
		
		return $this;
	}
	
	/**
	 * 组装SQL语句排序(ORDER BY)语句
	 * 
	 * 用于处理 ORDER BY post_id ASC 诸如之类的SQL语句部分
	 * @access public
	 * @param mixed $string 排序条件。注：本参数支持数组
	 * @return $this
	 */
	public function order($string) {
		
		//参数分析
		if (!$string) {
			return false;
		}
		
		//当参数为数组时
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
	 * 组装SQL语句LIMIT语句
	 * 
	 * limit(10,20)用于处理LIMIT 10, 20之类的SQL语句部分 
	 * @access public
	 * @param int $offset 启始id, 注:参数为整形
	 * @param int $count  显示的行数
	 * @return $this
	 */
	public function limit($offset, $count = null) {
		
		//参数分析
		$offset 	= (int)$offset;
		$count 		= (int)$count;
		
		$limit_str = !empty($count) ? $offset . ', ' . $count : $offset;
		$this->_parts['limit'] = ' LIMIT ' . $limit_str;
		
		return $this;
	}
	
	/**
	 * 组装SQL语句的LIMIT语句
	 * 
	 * 注:本方法与$this->limit()功能相类，区别在于:本方法便于分页,参数不同
	 * @access public
	 * @param int $page 	当前的页数
	 * @param int $count 	每页显示的数据行数
	 * @return $this
	 */
	public function page_limit($page, $count) {
		
		//参数分析
		$page		= (int)$page;
		$count	= (int)$count;

		$start_id = $count * ($page - 1);

		return $this->limit($start_id, $count);
	}
	
	/**
	 * 组装SQL语句中LEFT JOIN语句
	 * 
	 * jion('表名2', '关系语句')相当于SQL语句中LEFT JOIN 表2 ON 关系SQL语句部分
	 * @access public
	 * @param string $table_name	数据表名，注：本参数支持数组，主要用于数据表的alias别名
	 * @param string $where			join条件，注：不支持数组
	 * @return $this
	 */
	public function join($table_name, $where) {
		
		//参数分析
		if (!$table_name || !$where) {
			return false;
		}
		
		//处理数据表名
		if (is_array($table_name)) {									
			foreach ($table_name as $key=>$string) {																
				if (!empty($this->prefix)) {
					$table_name_str = is_int($key) ? $this->prefix . trim($string) : $this->prefix . trim($string) . ' AS ' . $key;
				} else {
					$table_name_str = is_int($key) ? trim($string) :  trim($string) .' AS ' . $key;	
				}
				//数据处理，只处理一个数组元素
				break;				
			}			
		} else {			
			$table_name_str = (!empty($this->prefix)) ? $this->prefix . trim($table_name) : trim($table_name);
		}
		
		//处理条件语句
		$where = trim($where);		
		$this->_parts['join'] = ' LEFT JOIN ' . $table_name_str . ' ON ' . $where;
		
		return $this;
	}
	
	/**
	 * 组装SQL的GROUP BY语句
	 * 
	 * 用于处理SQL语句中GROUP BY语句部分
	 * @access public
	 * @param mixed $group_name	所要排序的字段对象
	 * @return $this
	 */
	public function group($group_name) {
		
		//参数分析
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
	 * 组装SQL的HAVING语句
	 * 
	 * 用于处理 having id=2011 诸如此类的SQL语句部分
	 * @access pulbic
	 * @param string|array $where 条件语句
	 * @param string $value	数据表某字段的数据值
	 * @return $this
	 * 
	 * @example
	 * 用法与where()相似
	 */
	public function having($where, $value = null) {
		
		return $this->_having($where, $value, true);
	}
	
	/**
	 * 组装SQL的ORHAVING语句
	 * 
	 * 用于处理or having id=2011 诸如此类的SQL语句部分
	 * @access pulbic
	 * @param string|array $where 条件语句
	 * @param string $value	数据表某字段的数据值
	 * @return $this
	 * 
	 * @example
	 * 用法与where()相似
	 */
	public function orhaving($where, $value = null) {
		
		return $this->_having($where, $value, false);
	}
	
	/**
	 * 组装SQL的HAVING,ORHAVING语句
	 * 
	 * 为having()及orhaving()方法的执行提供'配件'
	 * @access protected
	 * @param mixed $where 条件语句
	 * @param string $value	数据表某字段的数据值
	 * @param boolean $is_having 当参数为true时，处理having()，当为false时，则为orhaving()
	 * @return $this
	 */
	protected function _having($where, $value = null, $is_having = true) {
		
		//参数分析
		if (!$where) {
			return false;
		}
		
		//分析参数条件，当参数为数组时
		if (is_array($where)) {						
			$where_array = array();
			foreach ($where as $string) {
				$where_array[] = trim($string);
			}
			
			$where = implode(' AND ', $where_array);
			unset($where_array);
		}
		
		//当程序$model->where('name=?', 'tommy');操作时
		if (!is_null($value)) {
			$value = $this->quote_into($value);
			$where = str_replace('?', $value, $where);
		}
		
		//分析having() 或 orhaving()
		if ($is_having == true) {
			$this->_parts['having'] 	= ($this->_parts['having']) ? $this->_parts['having'] . ' AND ' . $where : ' HAVING ' . $where;
		} else {
			$this->_parts['or_having'] 	= ($this->_parts['or_having']) ? $this->_parts['or_having'] . ' AND ' . $where : ' OR ' . $where;
		}
		
		return $this;
	}
	
	/**
	 * 执行SQL语句中的SELECT查询语句
	 * 
	 * 组装SQL语句并完成查询，并返回查询结果，返回结果可以是多行，也可以是单行
	 * @access public
	 * @param boolean $all_data 是否输出为多行数据，默认为true,即多行数据；当false时输出的为单行数据
	 * @return array
	 */
	public function select($all_data = true) {
		
		//分析查询数据表的语句
		if (!$this->_parts['from']) {
			return false;
		}
		
		//组装完整的SQL查询语句
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
	 * 字符串转义函数
	 * 
	 * SQL语句指令安全过滤,用于字符转义
	 * @access public
	 * @param mixed $value 所要转义的字符或字符串,注：参数支持数组
	 * @return string|string
	 */
	public static function quote_into($value) {
		
		//参数是否为数组
		if (is_array($value)) {			
			foreach ($value as $key=>$string) {
				$value[$key] = self::quote_into($string);
			}
		} else {
			//当参数为字符串或字符时
			if (is_string($value)) {
				$value = '\'' . addslashes($value) . '\'';
			}
		}
		
		return $value;
	}
	
	/*
	 * 获取数据的总行数
	 * 
	 * 获取某数据表满足一定条件的数据的总行数,分页程序常用
	 * @access public
	 * @param string $table_name	所要查询的数据表名
	 * @param string $field_name	所要查询字段名称
	 * @param string $where			查询条件
	 * @param string $value			数值（数据表某字段所对应的数据，通常都为字符串或字符）
	 * @return integer
	 */
	public function count($table_name, $field_name = null, $where = null, $value = null) {
		
		//参数判断
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
	 * 第三部分：对数据表的查询，更改，写入，删除操作。注：此函数均为数组型数据操作，非面向对象操作
	 */
	
	
	/**
	 * 对数据表的主键查询
	 * 
	 * 根据主键，获取某个主键的一行信息,主键可以类内设置。默认主键为数据表的物理主键
	 * 如果数据表没有主键，可以在model中定义
	 * @access public
	 * @param int|string|array $id 所要查询的主键值.注：本参数支持数组，当参数为数组时，可以查询多行数据
	 * @param array	$fields	返回数据的有效字段(数据表字段)
	 * @return string	所要查询数据信息（单行或多行）
	 * 
	 * @example
	 * 
	 * 实例化model
	 * $model = new DemoModel();
	 * 
	 * $model->find(1024);
	 * 
	 * 自定义model的主键
	 * $model->primary_key = 'memeber_name'; //数据类型并非int，也可以是varchar,char等类的,不过当主键数据类型为varchar,char时，参数要使用$this->quote_into()进行转义。
	 * 
	 * $name = $this->quote_into('tommy');
	 * $model->find($name);
	 * 
	 */
	public function find($id, $fields = null) {
		
		//参数分析
		if (!$id) {
			return false;
		}
		
		//获取主键及数据表名
		$this->get_table_name();
		$this->get_primary_key();
		
		//分析查询的字段信息		
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
	 * 获取数据表的全部数据信息
	 * 
	 * 以主键为中心排序，获取数据表全部数据信息. 注:如果数据表数据量较大时，慎用此函数，以免数据表数据量过大，造成数据库服务器内存溢出,甚至服务器宕机
	 * @access public
	 * @param array		$fields	返回的数据表字段,默认为全部.即SELECT * FROM table_name
	 * @param  boolean	$order_asc数据排序,若为true时为ASC,为false时为DESC, 默认为ASC
	 * @param integer	$offset	limit启起ID
	 * @param integer	$count	显示的行数
	 * @return array	数据表数据信息
	 */
	public function findAll($fields = null, $order_asc = true, $offset = null, $count = null) {
		
		//获取主键及数据表名
		$this->get_table_name();
		$this->get_primary_key();
		
		//分析查询的字段信息		
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
	 * 查询数据表单行数据
	 * 
	 * 根据一个查询条件，获取一行数据，返回数据为数组型，索引为数据表字段名
	 * @access public
	 * @param mixed 	$where 查询条件
	 * @param sring  	$value 数值
	 * @param array		$fields	返回数据的数据表字段,默认为全部字段.注：本参数为数组
	 * @return array 	所要查询的数据表数据
	 * 
	 * @example
	 * 
	 * 法一：
	 * $data = $model->getOne('name=?', 'tommy');
	 * 
	 * 法二：
	 * $data = $model->getOne(array('age>23', 'class=4'));
	 * 
	 * 法三：
	 * $data = $model->getOne('name=?', 'tommy', array('name', 'age', 'addr'));
	 */
	public function getOne($where, $value = null, $fields = null) {
		
		//参数分析
		if (!$where) {
			return false;
		}
		
		//获取数据表名
		$this->get_table_name();
		
		//分析查询的字段信息		
		$fields_str = $this->_parse_fields($fields);
		
		//处理查询的SQL语句
		$this->_parts['where'] = '';
		$this->where($where, $value);
		$where_str = $this->_parts['where'];
		unset($this->_parts['where']);
		
		$sql_str = 'SELECT ' . $fields_str . ' FROM ' . $this->table_name . $where_str;
		
		return $this->db->fetch_row($sql_str);
	}
	
	/**
	 * 查询数据表多行数据
	 * 
	 * 根据一个查询条件，获取多行数据。并且支持数据排序
	 * @access public
	 * @param mixed	$where 查询条件
	 * @param sring	$value 数值
	 * @param array $fields	返回数据的数据表字段.默认为全部字段.注:本参数为数组
	 * @param mixed $order 排序条件
	 * @param integer	$offset	limit启起ID
	 * @param integer	$count	显示的行数 
	 * @return array 
	 */
	public function getAll($where, $value=null, $fields = null, $order = null, $offset = null, $count = null) {
		
		//参数分析
		if (!$where) {
			return false;
		}
		
		//获取数据表名
		$this->get_table_name();
		
		//分析查询的字段信息		
		$fields_str = $this->_parse_fields($fields);
		
		$sql_str = 'SELECT ' . $fields_str . ' FROM '.$this->table_name;
		
		//处理查询的SQL语句
		$this->_parts['where'] = '';
		$this->where($where, $value);
		$sql_str .= $this->_parts['where'];
		unset($this->_parts['where']);
		
		//处理排序的SQL语句
		if (!is_null($order)) {
			$this->_parts['order'] = '';
			$this->order($order);
			$sql_str .= $this->_parts['order'];
			unset($this->_parts['order']);
		}
		
		//处理limit语句
		if (!is_null($offset)) {
			$this->_parts['limit'] = '';
			$this->limit($offset, $count);
			$sql_str .= $this->_parts['limit'];
			unset($this->_parts['limit']);
		}
		
		return $this->db->get_array($sql_str);
	}
	
	/**
	 * 数据表写入操作
	 * 
	 * 向当前model对应的数据表插入数据
	 * @access public
	 * @param array $data 所要写入的数据内容。注：数据必须为数组
	 * @return boolean
	 * 
	 * @example
	 * 
	 * $data = array('name'=>'tommy', 'age'=>23, 'addr'=>'山东'); //注：数组的键值是数据表的字段名
	 * 
	 * $model->insert($data);
	 */
	public function insert($data) {
		
		//参数分析
		if (!is_array($data) || !$data) {
			return false;
		}
		
		//获取数据表名及字段信息
		$this->get_table_name();
		$this->get_table_fields();

		//处理数据表字段与数据的对应关系
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
		
		//清空不必要的内存占用
		unset($field_array);
		unset($content_array);
		
		$sql_str = 'INSERT INTO ' . $this->table_name . ' (' . $field_str . ') VALUES (' . $content_str . ')';
				
		return $this->db->query($sql_str);
	}
	
	/**
	 * 数据表更改操作
	 * 
	 * 更改当前model所对应的数据表的数据内容
	 * @access public
	 * @param array 	$data 所要更改的数据内容
	 * @param mixed		$where 更改数据所要满足的条件
	 * @param string	$$params 数值，对满足更改的条件的进一步补充
	 * @return boolean
	 */
	public function update($data, $where, $params = null) {
		
		//参数分析
		if (!is_array($data) || !$data || !$where) {
			return false;
		}
		
		//获取数据表名及字段信息
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
		
		//组装SQL语句
		$sql_str = 'UPDATE ' . $this->table_name . ' SET ' . $content_str;
		
		//条件查询SQL语句的处理
		$this->_parts['where'] = '';
		$this->where($where, $params);
		$sql_str .= $this->_parts['where'];
		unset($this->_parts['where']);
		
		return $this->db->query($sql_str);
	}
	
	/**
	 * 数据表删除操作
	 * 
	 * 从当前model所对应的数据表中删除满足一定查询条件的数据内容
	 * @access public
	 * @param  mixed 	$where 所要满足的条件
	 * @param  sring	$value 数值，对满足条件的进一步补充
	 * @return boolean
	 */
	public function delete($where, $value = null) {
		
		//参数分析
		if (!$where) {
			return false;
		}
		
		//获取数据表名及字段信息
		$this->get_table_name();
		
		$sql_str = 'DELETE FROM ' . $this->table_name;
		
		//处理SQL的条件查询语句
		$this->_parts['where'] = '';
		$this->where($where, $value);
		$sql_str .= $this->_parts['where'];
		unset($this->_parts['where']);
		
		return $this->db->query($sql_str);
	}
	
	
	/**
	 * 第四部分:对数据表进行查询,更改,添加 等操作. 注：此方法返回数据类型为对象型，非数组型
	 */
	
	
	/**
	 * 新建一行数据，对象型的
	 * 
	 * @access public
	 * @return object
	 */
	public function createRow() {
		
		//初始化$this->myrow.
		$this->myrow = array();
		$this->myrow = (object)$this->myrow;
		
		return $this;
	}
	
	/**
	 * 数据表查询操作
	 *  
	 * 根据查询条件获取一行数据，对象型的
	 * @access public
	 * @param mixed		$where 查询条件
	 * @param string 	$value 数值，查询条件的进一步补充
	 * @return $this	
	 */
	public function fetchRow($where, $value = null) {
		
		//参数分析
		if (!$where) {
			return false;
		}
		
		$this->myrow = (object)$this->getOne($where, $value);
		return $this;
	}
	
	/**
	 * 数据表内容保存操作
	 * 
	 * 将对象型数据保存到数据表中，从而完成对数据的更改或添加操作
	 * @access public
	 * @return boolean
	 */
	public function save() {
		
		//当$this->myrow数据类型为非对象型时
		if (!is_object($this->myrow)) {
			return false;
		}
		
		//获取数据表主键
		$this->get_primary_key();
		
		//数据转换,转成可操作的数组型
		$myrow 		= (array)$this->myrow;		
		$key_array 	= array_keys($myrow);
		
		//当$key_array中存在数据表主键时，说明现在的$this->myrow的数据来自于查询，保存数据相当于数据更改
		if (in_array($this->primary_key, $key_array)) {			
			$where = '' . $this->primary_key . '=' . $this->quote_into($myrow[$this->primary_key]);
			$this->update($myrow, $where);			
		} else {
			$this->insert($myrow);
		}

		return true;
	}
	
	/**
	 * 第五部分：其它操作，包括执行SQL语句查询操作等
	 */
	
	
	/**
	 * 执行SQL查询语句
	 * 
	 * 执行一个SQL语句并获取执行后的全部数据
	 * @access public
	 * @param string $sql SQL语句
	 * @param boolean $all_rows 是否显示全部数据开关，当为true时，显示全部数据，为false时，显示一行数据，默认为true
	 * @return array 
	 */
	public function execute($sql, $all_data = true) {
		
		//参数分析
		if (!$sql) {
			return false;
		}
		
		return ($all_data == true) ? $this->db->get_array($sql) : $this->db->fetch_row($sql);
	}
	
	/**
	 * 执行SQL语句操作
	 * 
	 * 用于执行一个SQL语句，完成对数据表信息的更改，添加，删除等操作.注:如SQL查询语句应该使用execute(),因为query()返回值为boolean.
	 * @access public
	 * @param string $sql 所要执行的SQL语句
	 * @return boolean
	 */
	public function query($sql) {
		
		//参数分析
		if (!$sql) {
			return false;
		}
		
		return $this->db->query($sql);
	}
	
	/**
	 * 获取insert_id操作
	 * 
	 * 获取数据表上次写入操作时的insert_id
	 * @access public
	 * @return int	insert_id
	 */
	public function get_insert_id() {
		
		return $this->db->insert_id();
	}
	
	/**
	 * 自动变量设置
	 *  
	 * 程序运行时自动完成类中作用域为protected及private的变量的赋值 。
	 * 
	 * @access public
	 * @param string $name 属性名
	 * @param string $value 属性值
	 * @return void
	 */
	 public function __set($name, $value) {
	 	
	 	if (is_object($this->myrow)) {	 		
	 		$this->myrow->$name = addslashes($value);	 		
	 	} else {	 		
	 		//允许model对数据表名，数据表主键的自定义
	 		if (in_array($name, array('table_name', 'primary_key'))) {
	 			$this->$name = addslashes($value);
	 		}
	 	}
	 	
	 	return true; 	
	 }
	 
	 /**
	  * 输出类的实例化对象
	  * 
	  * 直接调用函数,输出内容,通常用来输出组装的SQL查询语句
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
	 * 析构函数
	 * 
	 * 当本类程序运行结束后，用于"打扫战场"，如：清空无效的内存占用等
	 * @access public
	 * @return void
	 */
	public function __destruct() {
		//清空不必要的内存占用		
		$unset_array = array($this->_parts, $this->myrow);
		foreach ($unset_array as $name) {
			//当变量存在时
			if (isset($name)) {
				unset($name);
			}			
		}
	}
}
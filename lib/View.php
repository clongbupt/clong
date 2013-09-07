<?php
//! View 类
 /**
 * 针对各个功能（list、post、delete）的各种View子类
 * 被Controller调用，完成不同功能的网页显示
 */
class View {
	
	protected $files=array('header' => 'default/header.php','content'=>'default/content.php','footer' =>'default/footer.php');
	protected $args=array();

	function __construct()  {
		
	}
	
	public function display($files ='') {  //输出最终格式化的HTML数据
		if (!empty($files))
			foreach ($files as $key => $value)
				foreach ($this->files as $k => $v)
					if ($key == $k)
						$this->files[$k] = $value;
		foreach ($this->files as $file)
			$this->_display($file);
	}
	
	//默认页面显示均来自default目录
	private function _display($file) {
		require_once(VIEW_PATH.$file);
	}

	public function set($key,$var) {
		// if (is_array($var))
			// return $this->_add($key,$var);
		// else
			return $this->_set($key,$var);
	}

	//for adding to an array
	// private function _add($key,$var) {
		// $this->args[$key][]=$var;
		// return $this;
	// }
	
	private function _set($key,$var) {
		$this->args[$key]=$var;
		return $this;
	}
}

class IndexView extends View
{
	function getContent () {
		require_once('view/index.php');
	}
}

class ResultView extends View 
{
	var $books;
	var $comments;
	var $searchContent;
	var $searchType;

	function __construct($books,$comments,$searchContent,$searchType) {
		$this->books = $books;
		$this->comments = $comments;
		$this->searchContent = $searchContent;
		$this->searchType = $searchType;
	}
	
	function getContent () {
		require_once('view/book/result.php');
	}
}

class AddCommentView extends View{
	var $book;
	var $comments;
	var $searchContent;
	var $searchType;
	
	function __construct ($book,$comments,$searchContent,$searchType){
		$this->book = $book;
		$this->comments = $comments;
		$this->searchContent = $searchContent;
		$this->searchType = $searchType;
	}
	
	function getContent(){
		require_once('view/comment/add.php');
	}
}

//重定向view
class RedirectView extends View   //显示所有留言的子类
{
	function getContent () {
	}
	
}

//错误view
class ErrorView extends View   //显示所有留言的子类
{
	var $msg;
	
	function __construct($msg)
	{
		$this->msg = $msg;
		parent::__construct();
	}
	
	//重置sidebar
	function getSidebar(){
	}
	
	function getContent() {
		?><br /> <?php echo $this->msg; ?><br /> 
		<a href="index.php">回到首页</a>
		<?php
	}
}
?>

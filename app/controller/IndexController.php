<?
//首页

class IndexController extends Controller{
	
	//测试页面显示
	public function indexAction ($params = '') {	
	
	$file = array("content" => "index.php");
	$this->view->display($file);
	
	}
	
	//测试跳转以及GET传参用
	public function testAction($params = ''){
		
		$params['test'] = "test";
		
		$this->redirect("result","index",$params);
	}
	
	//测试数据库调用
	public function testBookListAction($params =''){
		
		$bookObject = new Book();
		$books = $bookObject->findAll();
		
		$this->view->set("books",$books);
		
		$file = array("content"=>"book/content.php");
		$this->view->display($file);
	}
}
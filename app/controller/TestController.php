<?
class TestController extends Controller{
	
	public function indexAction ($params = '') {	
	
	// $file = array("content" => "index.php");
	// $this->view->display($file);
	$items = array("first"=>"a","second"=>"b","third"=>"c");
	$this->view->set("items",$items);
	
	$file = array("content" => "test/content.php");
	$this->view->display($file);
	}
}
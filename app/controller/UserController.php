<?
class UserController extends Controller{
	
	public function indexAction ($params = '') {	
	
		$file = array("header" => "user/header.php","content" => "user/index.php");
		$this->view->display($file);
	
	}
	
	public function searchAction ($params = ''){
	
		if (isset($_POST["userId"])&&$_POST["userId"]!="") $userId = $_POST["userId"];
		else $userId = "no";
		
		$userObject = & new User();
		
		$user = $userObject->getUserByUserId($userId);
		
		$this->view->set("userName",$user["name"]);
		
		$borrowObject = & new Borrow();
		$bookObject = & new Book();
		
		$borrows = $borrowObject->getBorrowInfoByUserId($userId);
		
		foreach ($borrows as & $borrow)
		{
			$book = $bookObject->getBookById($borrow['book_id']);
			$borrow['book_name'] = $book['book_name'];
		}
		
		$this->view->set('borrowBooks',$borrows);
		
		
		$file = array("header" => "user/header.php","content" => "user/list.php");
		$this->view->display($file);
	}
}
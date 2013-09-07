<?
//结果页
class ResultController extends Controller{
	
	
	public function indexAction ($params = '') {	

		if (isset($_POST["searchType"])&&$_POST["searchType"]!="") $searchType = $_POST["searchType"];
		elseif (isset($params["searchType"])&&$params["searchType"]!="")  $searchType = $params["searchType"];
		else $searchType = "bookName";
		
		$this->view->set("searchType",$searchType);
		
		if (isset($_POST["searchContent"])&&$_POST["searchContent"]!="") $searchContent = $_POST["searchContent"];
		elseif (isset($params["searchContent"])&&$params["searchContent"]!="") $searchContent = $params["searchContent"];
		else $searchContent = "all";
		
		$this->view->set("searchContent",$searchContent);
		
		if (!empty($params['page'])){
			$this->view->set("page",$params['page']);
		}
		$bookObject = & new Book();
		$books = $bookObject->getBooks($searchType,$searchContent);
		
		$books = $this->formateBookDate($books);
		$this->view->set("books",$books);
		
		$comments = array();
		if (is_array($books))
			foreach ($books as $key => $book)
			{
				$commentObject = & new Comment();
				$result = $commentObject->getCommentsByBookId($book['book_id']);
				if (!empty($result[0]['comment_id'])){
					$comments[$key] = $result;
					$comments[$key] = $this->formateCommentDate($comments[$key]);
				}
				else
					$comments[$key] = "none";
			}
		else 
			var_dump($books);
		$this->view->set("comments",$comments);
		
		$file = array("content" => "book/result.php");
		$this->view->display($file);
		
		//$this->view = & new ResultView($books,$comments,$searchContent,$searchType);
	
	}
	
	public function testAction($params = ''){
		header ("Location:index.php");
	}
	
	private function formateBookDate($books){
		if (is_array($books))
			foreach ($books as $book)
			{
				$time = strtotime($book['publish_date']);
				$book['publish_date'] = date("Y年m月d日",$time);
			}
		return $books;
	}
	
	private function formateCommentDate($comments){
		
		foreach ($comments as $comment)
		{
			$time = strtotime($comment['comment_date']);
			$comment['comment_date'] = date("Y年m月d日 H时i分s秒",$time);
		}
		
		return $comments;
	}
}
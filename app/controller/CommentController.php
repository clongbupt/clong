<?
class CommentController extends Controller{
	
	public function addAction($params = ''){
		
		if (isset($params['bookId'])&&!empty($params['bookId']))  $bookId = $params['bookId'];
		else $bookId = "no";
		
		if (isset($params['searchContent'])&&!empty($params['searchContent']))  $searchContent = $params['searchContent'];
		else $searchContent = "all";
		
		$this->view->set("searchContent",$searchContent);
		
		if (isset($params["searchType"])&&!empty($params["searchType"]))  $searchType = $params["searchType"];
		else $searchType = "bookName";
		
		$this->view->set("searchType",$searchType);
		
		if ($bookId != "no")
		{
			$bookObject = & new Book();
			$book = $bookObject->getBookById($bookId);
			
			$commentObject = & new Comment();
			$comments = $commentObject->getCommentsByBookId($bookId);
			if(is_array($comments))
				$comments = $this->formateCommentDate($comments);
		}
		else
			var_dump($bookId);
		
		if (!empty($params['page'])){
			$this->view->set("page",$params['page']);
		}
		
		$this->view->set("book",$book);
		$this->view->set("comments",$comments);
		
		$file = array("content" => "comment/add.php");
		$this->view->display($file);
	}
	
	public function addCommentAction ($params = ''){
		
		$args = array();
		
		if (isset($_POST["bookId"])&&!empty($_POST["bookId"]))  $args['bookId'] = $_POST["bookId"];
		else $args['bookId'] = "no";
		
		$params['bookId'] = $args['bookId'];
		
		if (isset($_POST["name"])&&!empty($_POST["name"]))  $args['commentName'] = $_POST["name"];
		else $args['commentName'] = "no";
		
		if (isset($_POST["level"])&&!empty($_POST["level"]))  $args['commentLevel'] = $_POST["level"];
		else $args['commentLevel'] = "no";
		
		if (isset($_POST["author"])&&!empty($_POST["author"]))  $args['commentAuthor'] = $_POST["author"];
		else $args['commentAuthor'] = "no";
		
		if (isset($_POST["content"])&&!empty($_POST["content"]))  $args['commentContent'] = $_POST["content"];
		else $args['commentContent'] = "no";	
		
		
		if (isset($_POST["searchContent"])&&!empty($_POST["searchContent"]))  $params['searchContent'] = $_POST["searchContent"];
		else $params['searchContent'] = "all";
		
		if (isset($_POST["searchType"])&&!empty($_POST["searchType"]))  $searchType = $_POST["searchType"];
		else $searchType = "bookName";
		
		$args['commentDate'] = date("YmdHis");	
		
		if ($args['bookId'] != "no"){
			$commentObject = & new Comment();
			if ($commentObject->addComment($args)){
				$this->redirect("comment","add",$params);
				
			}else
				var_dump($bookId);
		}
	}
	
	
	private function formateCommentDate($comments){
		
		foreach ($comments as $comment)
		{
			$time = strtotime($comment['comment_date']);
			$comment['comment_date'] = date("Y年m月d日 H时i分s秒",$time);
		}
		
		return $comments;
	}
	
	private function formateBookDate($book){
		$time = strtotime($book['publish_date']);
		$book['publish_date'] = date("Y年m月",$time);
		return $book;
	}

}
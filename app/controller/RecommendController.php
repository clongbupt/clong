<?
//首页

class RecommendController extends Controller{
	
	//测试页面显示
	public function indexAction ($params = '') {	
	
		$recommendObject = & new Recommend();
		$recommendBooks = $recommendObject->getRecommendBooks();
		$this->view->set("recommendBooks",$recommendBooks);
	
		$recommendIdArray = array();
		foreach ($recommendBooks as $key => $recommendBook)
			if (!empty($_COOKIE['Id'.$recommendBook["recommend_id"]]))
				$recommendIdArray[$key] = "1";
			else
				$recommendIdArray[$key] = "0";
			
		$this->view->set("recommendIdArray",$recommendIdArray);
	
		$file = array("header"=>"recommend/header.php","content" => "recommend/listRecommend.php");
		$this->view->display($file);
	
	}
	
	public function addAction ($params = ''){
		
		$file = array("header"=>"recommend/header.php","content" => "recommend/addRecommend.php");
		$this->view->display($file);
	}
	
	public function addRecommendAction ($params = ''){
		
		$args = array();
		
		if (isset($_POST["book_name"])&&!empty($_POST["book_name"]))  $args['bookName'] = $_POST["book_name"];
		else $args['bookName'] = "no";
		
		if (isset($_POST["isbn"])&&!empty($_POST["isbn"]))  $args['isbn'] = $_POST["isbn"];
		else $args['isbn'] = "no";
		
		if (isset($_POST["version"])&&!empty($_POST["version"]))  $args['version'] = $_POST["version"];
		else $args['version'] = "no";
		
		if (isset($_POST["author"])&&!empty($_POST["author"]))  $args['author'] = $_POST["author"];
		else $args['author'] = "no";
		
		if (isset($_POST["publisher"])&&!empty($_POST["publisher"]))  $args['publisher'] = $_POST["publisher"];
		else $args['publisher'] = "no";
		
		if (isset($_POST["price"])&&!empty($_POST["price"]))  $args['price'] = $_POST["price"];
		else $args['price'] = "no";
		
		if (isset($_POST["recommender"])&&!empty($_POST["recommender"]))  $args['recommender'] = $_POST["recommender"];
		else $args['recommender'] = "no";
		
		if (isset($_POST["recommender_email"])&&!empty($_POST["recommender_email"]))  $args['recommenderEmail'] = $_POST["recommender_email"];
		else $args['recommenderEmail'] = "no";
		
		if (isset($_POST["content"])&&!empty($_POST["content"]))  $args['content'] = $_POST["content"];
		else $args['content'] = "no";
		
		$args['recommendDate'] = date("YmdHis");
		
		if (!empty($args)){
			$recommendObject = & new Recommend();
			if ($recommendObject->addRecommendBook($args)){
				$this->redirect("recommend","index",$params);
				
			}else
				var_dump($args);
		}
		
	}
	
	public function ajaxAddNumAction(){
		
		if (json_decode($_POST["recommendId"]) != null)  $recommendId = json_decode($_POST["recommendId"]);
		else $recommendId = "no";
		
		if (json_decode($_POST["recommendNum"]) != null)  $recommendNum = json_decode($_POST["recommendNum"]);
		else $recommendNum = "no";
		
		if (empty($_COOKIE['Id'.$recommendId])){
		
			setcookie('Id'.$recommendId,$recommendId,time()+3600*24*30);
		
			$recommendObject = & new Recommend();
			
			if ($recommendObject->updateRecommendNumById($recommendId,$recommendNum+1))
			{
				$args["type"] = "success";
				$args["recommendNum"] = $recommendNum + 1;
				echo json_encode($args);
				exit;
			}
			else 
			{
				var_dump($recommendId.$recommendNum);
			}
		}
		else{
			setcookie('Id'.$recommendId,$recommendId,time()+3600*24*30);
			$args["type"] = "failed";
			echo json_encode($args);
			exit;
		}
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
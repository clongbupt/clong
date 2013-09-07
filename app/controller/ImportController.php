<?
class ImportController extends Controller{
	
	//测试页面显示
	public function indexAction ($params = '') {	
	
		if (!empty($_POST['operation'])) $operation = $_POST['operation'];
		else $operation = 0;
		if (!empty($_POST['isbn'])) $isbn = $_POST['isbn'];
		else $isbn = '0';
		switch ($operation)
		{
		case 1:
		  //图书入库，传入作者、图书名称、简介、图片地址、页数、价格、出版日期、出版社，操作数据库完成入库操作
		  
		  if (!empty($_POST['author'])) $author = $_POST['author'];
		else $author = 'author';
		if (!empty($_POST['name'])) $name = $_POST['name'];
		else $name = 'book_name';
		if (!empty($_POST['summary'])) $summary = $_POST['summary'];
		else $summary = 'summary';
		if (!empty($_POST['image'])) $image = $_POST['image'];
		else $image = 'image';
		if (!empty($_POST['pages'])) $pages = $_POST['pages'];
		else $pages = '0';
		if (!empty($_POST['price'])) $price = $_POST['price'];
		else $price = '0';
		if (!empty($_POST['pubdate'])) $pubdate = $_POST['pubdate'];
		else $pubdate = '1900-1-1';
		if (!empty($_POST['publisher'])) $publisher = $_POST['publisher'];
		else $publisher = 'default publisher';

		$bookObject = & new Book();
		
		if  ($bookObject->addBook($isbn,$author,$name,$price,$pubdate,$publisher,$summary))
			echo "SUCCESS";
		else echo "FAILED";

		  break;
		case 2:
		//借书操作，传入图书的isbn号以及借书者工号，操作数据库完成借书操作
		if (!empty($_POST['borrower'])) $borrower = $_POST['borrower'];
		else $borrower = 'borrower';
		  break;
		case 3:
		//还书操作，传入图书的isbn号，操作数据库完成还书操作

		  break;
		default:
			echo "error";
		  break;
		}
	
	}
}
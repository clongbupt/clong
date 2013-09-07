<?
class Book extends Model{
	
	public function __construct(){
		parent::__construct("book");
	}
	
	protected function get_primary_key() {
		return $this->primary_key = 'book_id';
	}

	protected function get_table_fields() {
		return $this->table_field = array (
							0 => 'book_id',
							1 => 'book_name',
							2 => 'isbn',
							3 => 'author',
							4 => 'category',
							5 => 'pages',
							6 => 'publisher',
							7 => 'publish_date',
							8 => 'import_date',
							9 => 'price',
							10 => 'status',
							11=> 'description',
							12=> 'image_url'
						);
	}
	
	public function getBooks($type,$content){
		
		if ($content === "all"){
			return $this->findAll();
			}
		else {
			$data = $this->quote_into($content);
			switch ($type){
				case "bookName":
					$selFormat="SELECT * FROM book WHERE book_name like '%%%s%%' and status = 0 ORDER BY book_id ASC";
					$query=sprintf($selFormat,$content);
					return $this->execute($query);
					//$this->select()->where->from()
				case "ISBN":
					$selFormat="SELECT * FROM book WHERE isbn like '%%%s%%' ORDER BY book_id ASC";
					$query=sprintf($selFormat,$content);
					return $this->execute($query);
				case "category":
					$selFormat="SELECT * FROM book WHERE category like '%%%s%%' ORDER BY book_id ASC";
					$query=sprintf($selFormat,$content);
					return $this->execute($query);
				case "author":
					$selFormat="SELECT * FROM book WHERE author like '%%%s%%' ORDER BY book_id ASC";
					$query=sprintf($selFormat,$content);
					return $this->execute($query);
				case "publisher":
					$selFormat="SELECT * FROM book WHERE publisher like '%%%s%%' ORDER BY book_id ASC";
					$query=sprintf($selFormat,$content);
					return $this->execute($query);
				case "status":
					$selFormat="SELECT * FROM book WHERE status like '%%%s%%' ORDER BY book_id ASC";
					$query=sprintf($selFormat,$content);
					return $this->execute($query);
				case "any":
					$selFormat="SELECT * FROM book WHERE book_name like '%%%s%%' or isbn like '%%%s%%' or author like '%%%s%%' or publisher like '%%%s%%' ORDER BY book_id ASC";
					$query=sprintf($selFormat,$content,$content,$content,$content);
					return $this->execute($query);
				case "anyOn":
					$selFormat="SELECT * FROM book WHERE ( book_name like '%%%s%%' or isbn like '%%%s%%' or author like '%%%s%%' or publisher like '%%%s%%' ) and status = 0 ORDER BY book_id ASC";
					$query=sprintf($selFormat,$content,$content,$content,$content);
					return $this->execute($query);
				default:
					var_dump($content);
					return FAILED;
			}
		}
	}
	
	public function getBookById($bookId)
	{
		$selFormat="SELECT * FROM book WHERE book_id='%d' ORDER BY book_id ASC";
		$query=sprintf($selFormat,$bookId);
		return $this->execute($query,false);
	}
	
	public function getBookNameById($bookId)
	{
		$selFormat="SELECT book_name FROM book WHERE book_id='%d' ORDER BY book_id ASC";
		$query=sprintf($selFormat,$bookId);
		return $this->execute($query,false);
	}
	
	public function addBook($isbn,$author,$name,$price,$pubdate,$publisher,$summary,$importDate,$status)
	{
		$selFormat = "INSERT INTO book(book_name,isbn,author,publisher,publish_date,import_date,price,status,description) VALUES ('%s','%s','%s','%s','%s','%s')";
		$query=sprintf($selFormat,$name,$isbn,$author,$publisher,$pubdate,$importDate,$price,$status,$summary);
		return $this->query($query);
	}
}
<?
class Comment extends Model{
	
	public function __construct(){
		parent::__construct("comment");
	}
	
	protected function get_primary_key() {
		return $this->primary_key = 'comment_id';
	}

	protected function get_table_fields() {
		return $this->table_field = array (
							0 => 'comment_id',
							1 => 'book_id',
							2 => 'comment_name',
							3 => 'comment_date',
							4 => 'comment_content',
							5 => 'comment_author',
							6 => 'comment_level'
						);
	}
	
	public function getComments (){
		$selFormat="SELECT * FROM comment";
		$query=sprintf($selFormat);
		return $this->execute($query);
	}
	
	public function getCommentById ($commentId){
		$selFormat="SELECT * FROM comment WHERE comment_id='%d'";
		$query=sprintf($selFormat,$commentId);
		return $this->execute($query);
	}
	
	public function getCommentsByBookId ($bookId){
		$selFormat="SELECT * FROM comment WHERE book_id = '%d'";
		$query=sprintf($selFormat,$bookId);
		return $this->execute($query);
	}
	
	public function addComment($args){
		$selFormat = "INSERT INTO comment(book_id,comment_name,comment_date,comment_content,comment_author,comment_level) VALUES ('%d','%s','%s','%s','%s','%s')";
		$query=sprintf($selFormat,$args['bookId'],$args['commentName'],$args['commentDate'],$args['commentContent'],$args['commentAuthor'],$args['commentLevel']);
		return $this->query($query);
	}
}
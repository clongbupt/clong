<?php
class Recommend extends Model{
	
	public function __construct(){
		parent::__construct("recommend");
	}
	
	protected function get_primary_key() {
		return $this->primary_key = 'recomment_id';
	}

	protected function get_table_fields() {
		return $this->table_field = array (
							0 => 'recomment_id',
							1 => 'isbn',
							2 => 'book_name',
							3 => 'version',
							4 => 'author',
							5 => 'publisher',
							6 => 'price',
							7 => 'recomment_num',
							8 => 'recommender',
							9 => 'recommend_email',
							10=> 'recommend_date'
						);
	}
	
	public function getRecommendBooks(){
		$selFormat="SELECT * FROM recommend ORDER BY recommend_num ASC";
		$query=sprintf($selFormat);
		return $this->execute($query);
	}
	
	public function getRecommendBooksByRecommendNum($recommendNum){
		$selFormat="SELECT * FROM recommend WHERE recommend_num='%d' ORDER BY recommend_num ASC";
		$query=sprintf($selFormat,$recommendNum);
		return $this->execute($query);
	}
	
	public function getRecommendBookByISBN($isbn){
		$selFormat="SELECT * FROM recommend WHERE isbn = '%d'";
		$query=sprintf($selFormat,$isbn);
		return $this->execute($query);
	}
	
	public function addRecommendBook($args){
		$selFormat = "INSERT INTO recommend(isbn,book_name,version,author,publisher,price,recommender,recommender_email,recommend_date) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s')";
		$query=sprintf($selFormat,$args['isbn'],$args['bookName'],$args['version'],$args['author'],$args['publisher'],$args['price'],$args['recommender'],$args['recommenderEmail'],$args['recommendDate']);
		return $this->query($query);
	}
	
	public function updateRecommendNumById($recommend_id,$recommend_num){
		$updFormat="UPDATE recommend SET 
					recommend_num = '%d'
					WHERE recommend_id='%d'";
		$query=sprintf($updFormat,$recommend_num,$recommend_id);
		return $this->query($query);
	}
}
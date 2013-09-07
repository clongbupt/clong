<?
class Borrow extends Model{
	
	public function __construct(){
		parent::__construct("borrow");
	}
	
	protected function get_primary_key() {
		return $this->primary_key = 'book_id';
	}

	protected function get_table_fields() {
		return $this->table_field = array (
							0 => 'book_id',
							1 => 'user_id',
							2 => 'borrow_date',
							3 => 'return_date',
							4 => 'renew_num'
						);
	}
	
	public function getBorrowInfoByUserId($userId){
		
		$selFormat="SELECT * FROM borrow WHERE user_id='%d'";
		$query=sprintf($selFormat,(int)$userId);
		return $this->execute($query);
	}
}
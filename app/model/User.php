<?
class User extends Model{
	
	public function __construct(){
		parent::__construct("reader");
	}
	
	protected function get_primary_key() {
		return $this->primary_key = 'user_id';
	}

	protected function get_table_fields() {
		return $this->table_field = array (
							0 => 'user_id',
							1 => 'name',
							2 => 'sex',
							3 => 'E-mail'
						);
	}
	
	public function getUserByUserId($userId){
		
		$selFormat="SELECT * FROM reader WHERE user_id='%d'";
		$query=sprintf($selFormat,$userId);
		return $this->execute($query,false);
	
	}
}
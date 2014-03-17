<?php
require_once('common.class.php');

class News extends Common{
	private $dbh = null;
	public function __construct() {
		parent::__construct();
		$this->dbh = new Database('slave');
	}
	public function getAllByNews($news){
		$result = array();
		if(empty($news)){
			return($result);
		}
		$sql = "select * from news where id = :news";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(':news', $news); 
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			$result[] = $row;
		}
		return($result);
	}

	function __destruct() {
        $this->dbh = null;
   }
}

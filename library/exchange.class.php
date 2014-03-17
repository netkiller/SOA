<?php
require_once('common.class.php');

class Exchange extends Common{
	private $dbh = null;
	public function __construct() {
		parent::__construct();
	}
	public function getOne()
	{
		$key = "info.example.com::exchange";
		$sql = "SELECT * FROM exchange";
		$this->dbh = new Database('slave');
		$stmt = $this->dbh->query($sql);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}
	function __destruct() {
       $this->dbh = null;
   }
}

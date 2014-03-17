<?php
require_once(APPPATH.'/common.class.php');

class Trades extends Common{
	private $dbh = null;
	public function __construct() {
		parent::__construct();
		$this->dbh = new Database('master');
	}

	public function selectSilverLoginVolumeByLastWeek(){
		$result = array();

		$start = date("Y-m-d", strtotime("last week"));
		$end = date("Y-m-d", strtotime("+5 day",strtotime("last week")));
		
		$sql = "select LOGIN,SUM(VOLUME) as VOLUME from TRADES where SYMBOL = 'SILVER' AND (CMD = 0 OR CMD = 1) AND CLOSE_TIME between :start AND :end GROUP BY LOGIN";
		
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue(':start', $start); 
		$stmt->bindValue(':end', $end); 
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//print_r($stmt);
		return($result);
	}

	function __destruct() {
	       $this->dbh = null;
  	}
}

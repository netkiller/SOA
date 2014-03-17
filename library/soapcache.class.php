<?php
require_once('common.class.php');

class Soapcache extends Common{
	private $dbh = null;
	public function __construct() {
		parent::__construct();
		$this->load('cache');
		$this->load('logging');
		$this->dbh = new Database('slave');
	}
	public function set($key,$value){
		$this->logging->info($key.':'.$value);
		return $this->cache->set($key,$value);
	}
	public function get($key){
		$tmp = $this->cache->get($key);
		$this->logging->info('get:'.$tmp);
		return $tmp;
	}
	function __destruct() {
		$this->dbh = null;
	}
}
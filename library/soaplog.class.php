<?php
require_once('common.class.php');

class Soaplog extends Common{
	private $dbh = null;
	public function __construct() {
		parent::__construct();
		$this->load('logging');
		$this->logging->format('/tmp','soaplog','log');
	}
	public function debug($msg){
		$this->logging->debug($msg);
	}
	public function info($msg){
		$this->logging->info($msg);
	}
	public function error($msg){
		$this->logging->error($msg);
	}
	public function warning($msg){
		$this->logging->warning($msg);
	}
	function __destruct() {
		$this->logging = null;
	}
}
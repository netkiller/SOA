<?php
if(!defined('BASEPATH')){
	define('BASEPATH', realpath(dirname(dirname(__FILE__))));
	define('APPPATH', BASEPATH.'/library');
	define('CFGPATH', BASEPATH.'/config');
	define('CONFIG_FILE', CFGPATH.'/'.'development.php');
}
if(!defined('CONFIG_FILE')){
	define('CONFIG_FILE', CFGPATH.'/'.'development.php');
}
	
include_once( BASEPATH. '/system/logging.class.php' );
use framework\log;
date_default_timezone_set('Asia/Hong_Kong');

class Database extends PDO {
    public function __construct($server, $dbname = null) {

		$config = include_once(CONFIG_FILE);

		$cfg = $config['database'][$server];

		if($dbname){
			$cfg['dbname'] = $dbname;
		}
		$cfg['dsn'] = "mysql:host=".$cfg['host'].";port=".$cfg['port'].";dbname=".$cfg['dbname'];
		
		parent::__construct($cfg['dsn'], $cfg['username'], $cfg['password'], 
			array(
				/*PDO::MYSQL_ATTR_INIT_COMMAND => "set names ".$cfg['charset'],*/
				PDO::ATTR_PERSISTENT => true,
				PDO::MYSQL_ATTR_COMPRESS => true
			)
		);
    }
};

Class Cache {
	public $cache = null;
	public function __construct() {
		$this->cache = new Redis();
		$this->pconnect('127.0.0.1',6379);
	}
	public function __destruct() {
	}
	public function pconnect($conn, $port = 6379){
		return $this->cache->pconnect($conn);
	}
	public function get($key){
		return $this->cache->get($key);
	}
	public function set($key, $value, $ttl = null){
		if($ttl){
			return $this->cache->set($key, $value, $ttl);
		}else{
			return $this->cache->set($key, $value);
		}
	}
	public function remove($key){
		return $this->cache->delete($key);
	}
	public function exists($key){
		return $this->cache->exists($key);
	}
	
}

Class Common{
	public $config;
	public $db;
	public $cache;
	public $logging;
	public function __construct() {
		
	}
	public function load($plugin, $param = null){
		if($plugin == 'cache'){
			$this->cache = new Cache();
		}
		if($plugin == 'logging'){
			$this->logging = new \framework\log\Logging($logfile = '/tmp/soap.log');
		}
		if($plugin == 'database'){
			$this->db = new Database($param);
		}
	}
	public function getConfig(){
		return $this->config;
	}
    public function database($server = null){
		
		
		$dbh = null;
		
		$config = include_once(CONFIG_FILE);
		$cfg = $config['database'][$server];
		$dsn = "mysql:host=".$cfg['host'].";port=".$cfg['port'].";dbname=".$cfg['dbname'];
		try {
			$dbh = new PDO($dsn, $cfg['username'], $cfg['password']);
			$dbh->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
		
		return $dbh;
		//if($dbname){
		//	return( new Database('master',$dbname) );
		//}else{
		//	return( new Database('master') );
		//}
    }
	
	public function test(){
		return "Test OK";
	}
	

};
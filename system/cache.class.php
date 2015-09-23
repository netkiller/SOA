<?php namespace framework\cache;

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

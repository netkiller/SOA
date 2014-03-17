<?php namespace framework;
class Framework{
	
	private $server = array();
	private $class = null;
	public function __construct() {
	
	}
	public function __destruct() {
		$this->logging->info('SOAP Server disconnect...');
	}
	private function acl(){
		if(in_array('HTTP_HOST',$this->config)){
			$http_host		= $this->server['HTTP_HOST'];
			$this->config['host'] = array('webservice.example.com');
			if(!in_array($http_host, $this->config['host'])){
				$except = sprintf("Permission host: %s", $http_host);
				$this->fault('Server',$except);
			}
		}
		if(in_array('HTTP_REFERER',$this->server)){
			$http_referer	= $this->server['HTTP_REFERER'];
		}
		$http_user_agent= $this->server['HTTP_USER_AGENT'];
		
		$remote_addr 	= $this->server['REMOTE_ADDR'];
		$firewall		= $this->config['firewall'];
		if(!in_array($remote_addr, $firewall)) {
			$except = sprintf("Permission denied: %s", $remote_addr);
			$this->fault('Server',$except);
		}
		
		$php_auth_user = $this->server['PHP_AUTH_USER'];
		$php_auth_pw = $this->server['PHP_AUTH_PW'];
		$this->logging->info(sprintf("SOAP Server connect %s, user %s", $remote_addr, $php_auth_user));
		return null;
	}
}
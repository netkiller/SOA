<?php namespace framework\restfull;

class RestfullFramework {
	private $server = array();
	private $postdata = array();
	private $class = null;
	private $function = null;
	private $logging = null;
	public function __construct($server = null, $postdata = null) {
		if(empty($server)){
			$this->server = $_SERVER;
		}else{
			$this->server = $server;
		}
		if(empty($postdata)){
			$this->postdata = file_get_contents("php://input");
		}else{
			$this->postdata = $postdata;
		}
		if(empty($this->postdata)){
			return null;
		}
		global $database;
		global $firewall;
		$this->config['database'] = $database;
		$this->config['firewall'] = $firewall;
		
		$this->logging = new \framework\log\Logging($logfile = '../log/restfull.'.date('Y-m-d').'.log');
		$this->logging->info('RestFull Server Starting...');
		
		$this->acl();
		print($this->run());
	}
	public function __destruct() {
		if($this->logging){
			$this->logging->info('Disconnect from: '. $this->remote_addr);
		}
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
		
		$this->remote_addr 	= $this->server['REMOTE_ADDR'];
		$firewall		= $this->config['firewall'];
		if(!in_array($this->remote_addr, $firewall)) {
			$except = sprintf("Permission denied: %s", $this->remote_addr);
			$this->fault('Server',$except);
		}
		if(in_array('PHP_AUTH_USER',$this->server)){
			$php_auth_user = $this->server['PHP_AUTH_USER'];
		}
		if(in_array('PHP_AUTH_PW',$this->server)){
			$php_auth_pw = $this->server['PHP_AUTH_PW'];
		}
		$this->logging->info('ACL permission: '. $this->remote_addr);
		return null;
	}	
	private function run(){
		$result = null;
		$request_uri = $this->server['REQUEST_URI'];
		$classspath = LIBRARY_DIR.strtolower($request_uri)  . '.class.php';
		if( is_file($classspath) ){
			require_once($classspath);
			$class = ucfirst(substr($request_uri, strrpos($request_uri, '/')+1));
			if (class_exists($class)) {
				$this->class = $class;
			}else{
				$msg = 'Object isnot exist.';
				$this->fault('Server',$msg);
			}
		}else{
			$msg = "Cannot loading interface!";
			$this->fault('Server',$msg);
		}
		
		if (class_exists($this->class)) {
			$protocol = json_decode($this->postdata,true);

			list ($func, $param) = each($protocol);
			if(method_exists($this->class, $func)){
				$obj = new $this->class;
				if (!$param){
					$tmp = $obj->$func();
					$result = json_encode($tmp);
					$this->logging->info($this->class.'->'.$func.'()');
				}else{
					$tmp = call_user_func_array(array($obj, $func), $param);
					$result = (json_encode($tmp));
					$this->logging->info($this->class.'->'.$func.'('.implode(",", $param).')');
				}
			}else{
				$this->fault('Object',$this->class. '->' . $func. ' isnot exist.');
			}
		}
		return $result;
	}


	private function fault($tag, $msg){
		$this->logging->exception($msg);
		throw new \Exception($tag.': '.$msg);
	}
}

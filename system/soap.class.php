<?php namespace framework\soap;

class SoapFramework extends \SoapServer{
	
	private $server = array();
	private $class 	= null;
	private $method	= null;
	public function __construct($server = null) {
		if(empty($server)){
			$this->server = $_SERVER;
		}else{
			$this->server = $server;
		}
		$this->post = file_get_contents("php://input"); 
		/*
		ini_set('soap.wsdl_cache_enabled', '0');
		ini_set('soap.wsdl_cache_ttl', '0'); 
		*/
		
		$this->config = include_once(CONFIG_FILE);
		parent::__construct(null, array('uri' => "http://api.example.com"));
		//date_default_timezone_set('Asia/Hong_Kong');
		$this->logging = new \framework\log\Logging($logfile = $this->config['logdir'].'log.'.date('Y-m-d').'.log');
		//$this->logging->debug($this->post);		
		
		
		
		$this->acl();
		$this->load();
		$this->setClass($this->class);
	}

	private function acl(){
		if(in_array('HTTP_HOST',$this->config)){
			$http_host		= $this->server['HTTP_HOST'];
			$this->config['host'] = array('webservice.example.com');
			if(!in_array($http_host, $this->config['host'])){
				$except = sprintf("Permission host: %s", $http_host);
				$this->logging->exception($except);
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
			$this->logging->exception($except);
			$this->fault('Server',$except);
		}

		$this->logging->info(sprintf("SOAP Server connect %s", $remote_addr));
		return null;
	}
	private function load(){
		$request_uri = $this->server['REQUEST_URI'];
		$classspath = APPPATH.strtolower($request_uri)  . '.class.php';
		if( is_file($classspath) ){
			$this->class = ucfirst(substr($request_uri, strrpos($request_uri, '/')+1));
			
			//if(in_array('PHP_AUTH_USER',$this->server)){
				$php_auth_user = $this->server['PHP_AUTH_USER'];
			//}
			//if(in_array('PHP_AUTH_PW',$this->server)){
				$php_auth_pw = $this->server['PHP_AUTH_PW'];
			//}
			
			if(preg_match("/<ns1:([\s\S]*?)([^>|\/]+)/i", $this->post, $matches)){
				$this->method = $matches[2];
				//$this->logging->debug($matches[2]);
			}
			if(array_key_exists($php_auth_user, $this->config['permission'])){
				$class  = substr($request_uri, 1, strrpos($request_uri, '/')).$this->class;
				//$this->logging->debug($class);
				//$this->logging->debug($this->class);
				if(array_key_exists($class, $this->config['permission'][$php_auth_user])){
					if(in_array($this->method, $this->config['permission'][$php_auth_user][$class])){
						$except = sprintf("Permission allow: %s->%s", $this->class, $this->method);
						$this->logging->info($except);
					}else{
						$except = sprintf("Permission denied: class %s, method %s", $this->class, $this->method);
						$this->logging->warning($except);
						$this->fault('Server',$except);
					}
				}else{
					$except = sprintf("Permission denied: class %s ", $class);
					$this->logging->warning($except);
					$this->fault('Server',$except);
				}
			}else{
				$except = sprintf("Permission denied: user %s ", $php_auth_user);
				$this->logging->warning($except);
				$this->fault('Server',$except);
			}
			
			require_once($classspath);
			if (class_exists($this->class)){
				if(!method_exists($this->class, $this->method)){
					$msg = 'Method isnot exist.';
					$this->fault('Server',$msg);
				}
			}else{
				$msg = 'Object isnot exist.';
				$this->fault('Server',$msg);
			}
			$this->logging->info('Loading '.$this->class.'->'.$this->method);
		}else{
			$msg = "Cannot loading interface!";
			$this->fault('Server',$msg);
		}
		return null;
	}	
	public function __destruct() {
		$this->logging->info('SOAP Server disconnect...'."\n------");
	}
}
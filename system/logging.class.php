<?php namespace framework\log;

class Logging {
	protected $file;
	protected $logfile;
	public function __construct($logfile = null, $debug=false){
		if($logfile){
			$this->logfile = $logfile;
		}
		if(!file_exists(dirname($this->logfile))){
			throw new \Exception('Directory '. dirname($this->logfile) ." isn't exist.");
		}
		if(file_exists($this->logfile)){
			if(!is_writable($this->logfile)){
				throw new \Exception('Directory '. $this->logfile ." isn't writable.");
			}
		}
		$this->file = fopen($this->logfile,"a+");
		$this->isdebug = $debug;
	}
	public function __destruct() {
		if($this->file){
			fclose($this->file);
		}
	}
	public function filename($logfile = null){
		if($logfile){
			$this->logfile = $logfile;
		}
	}
	public function format($logpath = '/tmp', $prefix = "debug", $suffix = "log") {
		$this->logfile = $logpath.'/'.$prefix.'.'.date('Y-m-d.H:i:s').'.'. $suffix;
	}
	private function write($msg){
		if($this->file){
			fwrite($this->file,date('Y-m-d H:i:s')."\t".$msg."\r\n");
		}
	}
	public function info($msg){
		$this->write(__FUNCTION__."\t".$msg);
	}
	public function warning($msg){
		$this->write(__FUNCTION__."\t".$msg);
	}
	public function error($msg){
		$this->write(__FUNCTION__."\t".$msg);
	}
	public function debug($msg){
		if($this->isdebug){
			$this->write(__FUNCTION__."\t".$msg);
		}
	}
	public function exception($msg){
		$this->write(__FUNCTION__."\t".$msg);
	}

}

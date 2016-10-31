<?php namespace framework\log;

class Logging {
	protected $logfile;
	private $thread;

	public function __construct($logfile = null, $debug=false, $thread = null){
		if($logfile){
			$this->logfile = $logfile;
			$this->logfile = sprintf($this->logfile, date ( 'Y-m-d' ));
	
			if(!file_exists(dirname($this->logfile))){
				throw new \Exception('Directory '. dirname($this->logfile) ." isn't exist.");
			}
			if(file_exists($this->logfile)){
				if(!is_writable($this->logfile)){
					throw new \Exception('Directory '. $this->logfile ." isn't writable.");
				}
			}
		}
		//$this->file = fopen($this->logfile,"a+");
		$this->isdebug = $debug;
		if($thread){
			$this->thread = $thread;
		}
	}
	public function __destruct() {
		//if($this->file){
			//fclose($this->file);
		//}
	}
	public function filename($logfile = null){
		if($logfile){
			$this->logfile = $logfile;
		}
	}
	public function format($logpath = '/tmp', $prefix = "debug", $suffix = "log") {
		$this->logfile = $logpath.'/'.$prefix.'.'.date('Y-m-d.H:i:s').'.'. $suffix;
	}
	
	public function setThreadId($thread){
		$this->thread = $thread;
	}
	
	private function write($msg){
		//if($this->file){
			//fwrite($this->file,date('Y-m-d H:i:s')."\t".$msg."\r\n");
		//}
		$output = sprintf("%s %s %s\r\n", date('Y-m-d H:i:s'), $this->thread, $msg);
		file_put_contents($this->logfile, $output, FILE_APPEND | LOCK_EX);
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

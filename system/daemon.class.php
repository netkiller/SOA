<?php
/*
 * PHP Daemon sample.
 * Home: http://netkiller.github.io
 * Author: netkiller<netkiller@msn.com>
 * 
*/

namespace framework\daemon;

require_once( __DIR__.'/autoload.class.php' );

class Logger {
	
	public function __construct(/*Logging $logger*/) {
	}

	public function logger($type, $message) {
		$log = sprintf ( "%s\t%s\t%s\n", date ( 'Y-m-d H:i:s' ), $type, $message );
		file_put_contents ( sprintf(__DIR__."/../log/sender.%s.log", date ( 'Y-m-d' )), $log, FILE_APPEND );
	}
	
}

final class Signal{	
    public static $signo = 0;
	protected static $ini = null;
	public static function set($signo){
		self::$signo = $signo;
	}
	public static function get(){
		return(self::$signo);
	}
	public static function reset(){
		self::$signo = 0;
	}
}

class Test extends Logger {
	//public static $signal = null;
	
	public function __construct() {
		//self::$signal == null;
	}
	public function run(){
		while(true){
			pcntl_signal_dispatch();
			printf(".");
			sleep(1);
			if(Signal::get() == SIGHUP){
				Signal::reset();
				break;
			}
		}
		printf("\n");
	}
}

class Daemon extends Logger {
	/* config */
	const LISTEN = "tcp://192.168.2.15:5555";
	const pidfile 	= __CLASS__;
	const uid		= 80;
	const gid		= 80;
	const sleep	= 5;

	protected $pool 	= NULL;
	protected $config	= array();

	public function __construct($uid, $gid, $class) {
		$this->pidfile = '/var/run/'.basename(get_class($class), '.php').'.pid';
		//$this->config = parse_ini_file('sender.ini', true); //include_once(__DIR__."/config.php");
		$this->uid = $uid;
		$this->gid = $gid;
		$this->class = $class;
		$this->classname = get_class($class);
		
		$this->signal();
	}
	public function signal(){

		pcntl_signal(SIGHUP,  function($signo) /*use ()*/{
			//echo "\n This signal is called. [$signo] \n";
			printf("The process has been reload.\n");
			Signal::set($signo);
		});

	}
	private function daemon(){
		if (file_exists($this->pidfile)) {
			echo "The file $this->pidfile exists.\n";
			exit();
		}

		$pid = pcntl_fork();
		if ($pid == -1) {
			 die('could not fork');
		} else if ($pid) {
			 // we are the parent
			 //pcntl_wait($status); //Protect against Zombie children
			exit($pid);
		} else {
			file_put_contents($this->pidfile, getmypid());
			posix_setuid(self::uid);
			posix_setgid(self::gid);
			return(getmypid());
		}
	}
	private function run(){

		while(true){
			
			printf("The process begin.\n");
			$this->class->run();
			printf("The process end.\n");
			
		}
	}
	private function foreground(){
		$this->run();
	}
	private function start(){
		$pid = $this->daemon();
		for(;;){
			$this->run();
			sleep(self::sleep);
		}
	}
	private function stop(){

		if (file_exists($this->pidfile)) {
			$pid = file_get_contents($this->pidfile);
			posix_kill($pid, 9);
			unlink($this->pidfile);
		}
	}
	private function reload(){
		if (file_exists($this->pidfile)) {
			$pid = file_get_contents($this->pidfile);
			//posix_kill(posix_getpid(), SIGHUP);
			posix_kill($pid, SIGHUP);
		}
	}	
	private function status(){
		if (file_exists($this->pidfile)) {
			$pid = file_get_contents($this->pidfile);
			system(sprintf("ps ax | grep %s | grep -v grep", $pid));
		}
	}
	private function help($proc){
		printf("%s start | stop | restart | status | foreground | help \n", $proc);
	}
	public function main($argv){

		if(count($argv) < 2){
			$this->help($argv[0]);
			printf("please input help parameter\n");
			exit();
		}
		if($argv[1] === 'stop'){
			$this->stop();
		}else if($argv[1] === 'start'){
			$this->start();
        }else if($argv[1] === 'restart'){
			$this->stop();
            $this->start();
		}else if($argv[1] === 'status'){
			$this->status();
		}else if($argv[1] === 'foreground'){
			$this->foreground();
		}else if($argv[1] === 'reload'){
			$this->reload();
		}else{
			$this->help($argv[0]);
		}
	}
}
/*
$daemon = new Daemon(80,80, new Test());
$daemon->main($argv);
*/
?>



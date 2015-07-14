<?php

include_once(__DIR__.'/../system/rabbitmq.class.php');

class RabbitDaemon{
	/* config */
	//const LISTEN = "tcp://192.168.2.15:5555";
	const pidfile 	= __CLASS__;
	const uid		= 80;
	const gid		= 80;
	const sleep		= 60;

	protected $pool 	= NULL;
	protected $config	= array();

	public function __construct() {
		$this->pidfile = '/var/run/'.self::pidfile.'.pid';
		//$this->config = include_once("config.php");
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
			// we are the child
			file_put_contents($this->pidfile, getmypid());
			posix_setuid(self::uid);
			posix_setgid(self::gid);
			return(getmypid());
		}
	}
	private function start(){
		$pid = $this->daemon();
		for(;;){
			$rabbit = new RabbitMQ($exchangeName = 'email', $queueName = 'email', $routeKey = 'email');
			$rabbit->main();
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
	private function status(){
		if (file_exists($this->pidfile)) {
			$pid = file_get_contents($this->pidfile);
			system(sprintf("ps ax | grep %s | grep -v grep", $pid));
		}
	}
	private function help($proc){
		printf("%s start | stop | restart | status | help \n", $proc);
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
		}else{
			$this->help($argv[0]);
		}
	}
}

$daemon = new RabbitDaemon();
$daemon->main($argv);
?>



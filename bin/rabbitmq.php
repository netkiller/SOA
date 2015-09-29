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

	public function __construct($queue, $exchange, $route) {
		$this->pidfile = '/var/run/'.self::pidfile.'.pid';
		$this->queue 	= $queue;
		$this->exchange = $exchange;
		$this->route 	= $route;
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
			exit($pid);
		} else {
			file_put_contents($this->pidfile, getmypid());
			posix_setuid(self::uid);
			posix_setgid(self::gid);
			return(getmypid());
		}
	}
	private function run(){
		
		$rabbit = new \framework\RabbitMQ($this->queue, $this->exchange, $this->route);
		$rabbit->main();
		
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
	private function status(){
		if (file_exists($this->pidfile)) {
			$pid = file_get_contents($this->pidfile);
			system(sprintf("ps ax | grep %s | grep -v grep", $pid));
		}
	}
	private function help($proc){
		printf("%s start | stop | restart | foreground | status | help \n", $proc);
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
        }else if($argv[1] === 'foreground'){
			$this->foreground();
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

$daemon = new RabbitDaemon($queueName = 'example', $exchangeName = 'email', $routeKey = 'email');
$daemon->main($argv);
?>



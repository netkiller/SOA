<?php
namespace framework;

class Config{
	protected $config = null;
	/*
	array(
		'db' => array(
			'host' => '192.168.6.1',
			'port' => '3306',
			'login' => 'guest',
			'password' => 'guest',
			'db'=>''
		),
		'mq' => array(
			'host' => '192.168.6.1',
			'port' => '5672',
			'login' => 'guest',
			'password' => 'guest',
			'vhost'=>'/'
		)
	);
	*/
	public function __construct($cfgfile = 'rabbitmq.ini') {
		$this->config = parse_ini_file(__DIR__.'/../config/'.$cfgfile, true);
	}

	public function get($key = null){
		if($key){
			return($this->config[$key]);
		}else{
			return($this->config);
		}
	}
	public function getArray($sect = null){
		if($sect){
			return($this->config[$sect]);
		}else{
			return($this->config[$this->section]);
		}
	}
}

//$config = new Config('rabbitmq.ini');
//print_r($config->get());
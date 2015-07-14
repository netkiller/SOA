<?php
namespace framework;

class Config{
	protected $config = array(
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
	public function __construct($section) {
		$this->section = $section;
	}
	public function get($key = null){
		if($key){
			return($this->config[$this->section][$key]);
		}else{
			return($this->config[$this->section]);
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

<?php

class Backend {
	public static function Factory($class) {
		$options = array('uri' => "http://api.example.com",
			'location'=>'http://api.example.com/backend/'.$class,
			'compression' => 'SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP',
			'login'=>'backend',
			'password'=>'passw0rd',
			/* 'soap_version' => SOAP_1_2,*/
			'trace'=>true
		);
		$client = new SoapClient(null, $options);
		return($client);
	}
}

class Frontend {
	public static function Factory($class) {
		$options = array('uri' => "http://api.example.com",
			'location'=>'http://api.example.com/frontend/'.$class,
			'compression' => 'SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP',
			'login'=>'frontend',
			'password'=>'passw0rd',
			/* 'soap_version' => SOAP_1_2,*/
			'trace'=>true
		);
		$client = new SoapClient(null, $options);
		return($client);
	}
}


SOA
===
SOA Framework

Install
-----

	# mkdir -p /www/example.com/api.example.com
	# cd /www/example.com/api.example.com
	# git clone https://github.com/netkiller/SOA.git .
	
	# chown www:www -R /www/example.com/api.example.com
	
### Nginx 
	
	Copy sample file to nginx directory.
	
	# cp doc/nginx.conf /etc/nginx/conf.d/api.example.com.conf
	
	Create a user for nginx.
	
	# htpasswd -c -d /etc/nginx/htpasswd test
	New password: 
	Re-type new password: 
	Adding password for user test
	
	Reload nginx.
	
	#systemctl reload nginx
	
	You can see log file.
	
	# ll /var/log/nginx/api.example.com.*
	-rw-r--r-- 1 root root 0 Sep 11 16:38 /var/log/nginx/api.example.com.access.log
	-rw-r--r-- 1 root root 0 Sep 11 16:38 /var/log/nginx/api.example.com.error.log
	
Configure
-----

	# vim config/development.php
	
	Add your ip address to config file.
	
	$firewall = array('192.168.4.1','192.168.6.20','192.168.2.13','192.168.2.52','192.168.2.38');
	
	The following is database.
	
	'master' => array(
		'host' => '192.168.2.1',
		'port' => '3306',
		'database' => 'example',
		'username' => 'www',
		'password' => 'password',
		'charset' => 'UTF8',
		'client_flags' => 'MYSQL_CLIENT_COMPRESS'
	),
	'slave' => array(
		'host' => '192.168.4.1',
		'port' => '3306',
		'database' => 'example',
		'username' => 'readonly',
		'password' => 'password',
		'charset' => 'UTF8',
		'client_flags' => 'MYSQL_CLIENT_COMPRESS'
	),

Getting Start
-----

### Create a SOA class 

	# cat library/hello.class.php 
	<?php
	require_once('common.class.php');

	class Hello extends Common{
		private $dbh = null;
		public function __construct() {
			parent::__construct();
			//$this->dbh = new Database('slave');
		}
		public function world(){
			return("helloworld!!!");
		}

		function __destruct() {
			//$this->dbh = null;
	   }
	}	
	
### Create a test script.
	
	# cat test/hello.soap.php 
	<?php

	$options = array('uri' => "http://api.example.com",
					'location'=>'http://api.example.com/hello',
									 'compression' => 'SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP',
									'login'=>'test',
									'password'=>'passw0rd',
					'trace'=>true
									);
	$client = new SoapClient(null, $options);

	try {

			print_r($client->world());

	}
	catch (Exception $e)
	{
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}	
	
### open file config/development.php and then add item permission. 
	
	$permission = array(
		'backend' => array(
			'backend/Trades'=> array('selectSilverLoginVolumeByLastWeek','',''),
		),
		'frontend' => array(
			'frontend/Members'=> array('','',''),
			'frontend/Exchange'=> array('getOne','','')
		),
		'anonymous' => array(
			'News'=> array('','',''),
			'RSS'=> array('','','')
		),
		'test' => array(
			'Hello' => array('world'),
		)
	);
	
### Test

	# php test/hello.soap.php 
	helloworld!!!
<?php
return array(

	'logdir' => '../log/',

	'firewall' => array('127.0.0.1','192.168.2.38'),

	'database' => array(
		'master' => array(//example.com 主库（初始化默认连接）
			'host' => '192.168.2.1',
			'port' => '3306',
			'database' => 'example',
			'username' => 'www',
			'password' => 'qwer123',
			'charset' => 'UTF8',
			'client_flags' => 'MYSQL_CLIENT_COMPRESS'
		),
		'slave' => array(//从库，暂时没有调用
			'host' => '192.168.2.1',
			'port' => '3306',
			'database' => 'example',
			'username' => 'www',
			'password' => 'qwer123',
			'charset' => 'UTF8',
			'client_flags' => 'MYSQL_CLIENT_COMPRESS'
		),
		'infomaster' => array( //info.example.com数据库
			'host' => 'localhost',
			'port' => '3306',
			'database' => 'info.example.com',
			'username' => 'example',
			'password' => 'DA2ESu8eK73arAKOduj5H2hi6ur81A',
			'charset' => 'UTF8',
			'client_flags' => 'MYSQL_CLIENT_COMPRESS'
		),
		'infoslave' => array( //从库，暂时不用
			'host' => '192.168.2.1',
			'port' => '3306',
			'database' => 'example_info',
			'username' => 'www',
			'password' => 'qwer123',
			'charset' => 'UTF8',
			'client_flags' => 'MYSQL_CLIENT_COMPRESS'
		),
		'news'=>array( //新闻发布专用
			'host' => 'localhost:/tmp/mysql.sock',
			'port' => '',
			'database' => 'real_example',
			'username' => 'root',
			'password' => 'qwer123',
			'charset' => 'UTF8',
			'client_flags' => 'MYSQL_CLIENT_COMPRESS'
		),
		'mt4'=>array(
			'host' => 'mt4db.example.com',
			'port' => '3306',
			'database' => 'example',
			'username' => 'www',
			'password' => 'qwer123',
			'charset' => 'UTF8',
			'client_flags' => 'MYSQL_CLIENT_COMPRESS'
		),
	),

	'plugin'  => array(
	
	),
	'soapclient' => array(
		'uri' => 'http://webservice.example.com',
		'login' => 'soap',
		'password' => 'Cmt4khceZAJMkHm8',
		'compression' => 'SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP',
	),

	'permission' => array(
		'soap' => array(
			'Members'=> array('getAllByUsernameAndMobile','getAllByLimit',''),
			'Exchange'=> array('getOne','',''),
			'Trades'=> array('getModifytimeProfitCommentByLogin','','')
		),
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
		)
	),

);
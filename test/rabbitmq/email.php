<?php
//require_once( __DIR__.'/autoload.class.php' );

$exchangeName = 'email';
$queueName = 'email';
$routeKey = 'email';
$mail = $argv[1];
$message = empty($argv[2]) ? 'Hello World!' : ' '.$argv[2];

 
$connection = new AMQPConnection(array(
	'host' => '192.168.6.1', 
	'port' => '5672', 
	'vhost' => '/', 
	'login' => 'guest', 
	'password' => 'guest'
	));
$connection->connect() or die("Cannot connect to the broker!\n");
 
$channel = new AMQPChannel($connection);
$exchange = new AMQPExchange($channel);
$exchange->setName($exchangeName);
$queue = new AMQPQueue($channel);
$queue->setName($queueName);
$queue->setFlags(AMQP_DURABLE);
$queue->declareQueue();

$message ='{"Class":"Email","Method":"smtp","Param":["Neo","'.$mail.'","Test","Helloworld!!! '.$message.'"]}';
$exchange->publish($message, $routeKey);
var_dump("[x] Sent $message");


$connection->disconnect();

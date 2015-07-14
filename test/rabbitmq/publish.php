<?php
//require_once( __DIR__.'/autoload.class.php' );

$exchangeName = $argv[1];
$queueName = $argv[2];
$routeKey = $argv[3];
$message = empty($argv[4]) ? 'Hello World!' : ' '.$argv[4];

//$message ='{"Class":"Test","Method":"sum","Param":[3,2]}';
//$message ='{"Class":"Test","Method":"sum","Param":['.$argv[4].']}';
 
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

for($i=1;$i<10000000;$i++) {

//$message ='{"Class":"Test","Method":"sum","Param":['.rand(1,100000).','.rand(1,100000).']}';
$message ='{"Class":"Test","Method":"sum","Param":['.$i.','.rand(1,100000).']}';
echo $message;
$exchange->publish($message, $routeKey);

$exchange->publish('{"Class":"String","Method":"strrev","Param":["'.rand(1,100000000).'"]}', $routeKey);
var_dump("[x] Sent $message");

}

$connection->disconnect();

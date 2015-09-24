<?php
//require_once( __DIR__.'/autoload.class.php' );

$queueName = 'example';
$exchangeName = 'email';
$routeKey = 'email';
$mail = $argv[1];
$subject = $argv[2];
$message = empty($argv[3]) ? 'Hello World!' : ' '.$argv[3];

 
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

$msg = array(
	'Namespace'=>'namespace',
	"Class"=>"Email",
	"Method"=>"smtp",
	"Param" => array(
		$mail, $subject, $message, null
	)
);

//$message ='{"Class":"Email","Method":"smtp","Param":["'.$mail.'","Test","Helloworld!!! '.$message.'"]}';
//$exchange->publish($message, $routeKey);
//var_dump("[x] Sent $message");


$exchange->publish(json_encode($msg), $routeKey);
printf("[x] Sent %s \r\n", json_encode($msg));


$connection->disconnect();

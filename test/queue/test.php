<?php
//require_once( __DIR__.'/autoload.class.php' );

$queueName = 'example';
$exchangeName = 'email';
$routeKey = 'email';
 
$connection = new AMQPConnection(array(
	'host' => '192.168.4.1', 
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

$min = 0;
$max = 10000;

while(true){



$msg = array(
	'Namespace'=>'namespace',
	"Class"=>"Test",
	"Method"=>"sum",
	"Param" => array(
		rand ( $min , $max ),
		rand ( $min , $max )
	)
);

//$message ='{"Class":"Email","Method":"smtp","Param":["'.$mail.'","Test","Helloworld!!! '.$message.'"]}';
//$exchange->publish($message, $routeKey);
//var_dump("[x] Sent $message");


$exchange->publish(json_encode($msg), $routeKey);
printf("[x] Sent %s \r\n", json_encode($msg));
}

$connection->disconnect();

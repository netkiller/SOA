<?php

include_once(__DIR__.'/../system/rabbitdaemon.class.php');

$daemon = new \framework\RabbitDaemon($queueName = 'example', $exchangeName = 'email', $routeKey = 'email');
$daemon->main($argv);
?>



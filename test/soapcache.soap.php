<?php
include_once('../config/default.php');

$options = $soap;
$options['location'] = 'http://webservice.example.com/soapcache';

$client = new SoapClient(null, $options);

try {

	print_r($client->set('hello','Helloworld'));
	print_r($client->set('os','Linux'));
	print_r($client->get('hello'));
	print_r($client->get('os'));
	 
}
catch (Exception $e)
{
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}


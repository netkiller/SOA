<?php
include_once('../config/default.php');

$options = $soap;
$options['location'] = 'http://webservice.example.com/soaplog';

$client = new SoapClient(null, $options);

try {

	print_r($client->info('Helloworld'));
	print_r($client->debug('Linux'));
	print_r($client->warning('hello'));
	print_r($client->error('os'));

}
catch (Exception $e)
{
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}
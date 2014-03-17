<?php
include_once('../config/default.php');

$options = $soap;
$options['location'] = 'http://webservice.example.com/exchange';

$client = new SoapClient(null, $options);

try {

	print_r($client->getOne());
   
} 
catch (Exception $e) 
{ 
    echo 'Caught exception: ',  $e->getMessage(), "\n"; 
} 


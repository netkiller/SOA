<?php

$options = array('uri' => "http://webservice.example.com",
                'location'=>'http://webservice.example.com/backend/trades',
				 'compression' => 'SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP',
				'login'=>'soap',
				'password'=>'Cmt4khkHm8eZA',
                'trace'=>true
				);
$client = new SoapClient(null, $options);

try {

	print_r($client->selectSilverLoginVolumeByLastWeek());
   
} 
catch (Exception $e) 
{ 
    echo 'Caught exception: ',  $e->getMessage(), "\n"; 
} 


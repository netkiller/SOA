<?php

$options = array('uri' => "http://webservice.example.com",
                'location'=>'http://webservice.example.com/news',
                                 'compression' => 'SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP',
                                'login'=>'neo',
                                'password'=>'chen',
                'trace'=>true
                                );
$client = new SoapClient(null, $options);

try {

        print_r($client->getAllByNews('1'));

}
catch (Exception $e)
{
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}

<?php
define ('BASEPATH', realpath(dirname(dirname(__FILE__))));
define ('APPPATH', BASEPATH.'/library');
define ('CFGPATH', BASEPATH.'/config');
define ('FRAMEWORK_DIR', BASEPATH.'/system');
define ('DEBUG', false);
//define ('DEBUG', ture);
require_once(FRAMEWORK_DIR. '/soap.class.php');
include_once( FRAMEWORK_DIR. '/logging.class.php' );
use framework\soap;
use framework\log;

try {

	$soapframework = new framework\soap\SoapFramework($_SERVER);
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$soapframework->handle();
	} 
	/*
	else {
		echo "This SOAP server can handle following functions: ";
		$functions = $soapframework->getFunctions();
		foreach($functions as $func) {
		echo $func . "\n";
	}
	*/
	
} catch (SoapFault $exc) {
	echo $exc->getTraceAsString();
}
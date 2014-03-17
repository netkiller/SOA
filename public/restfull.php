<?php
//$arr = array ('getAllByLimit'=> array(2,10));
//echo json_encode($arr);
//{"getAllByLimit":{"limit":2,"offset":10}}
//curl -d '{"getOne":""}' http://restfull.example.com/exchange
//curl -d '{"getAllByLimit":[20,20]}' http://restfull.example.com/members

define ('BASEPATH', realpath(dirname(dirname(__FILE__))));
define('APPPATH', BASEPATH.'/library');
define('CFGPATH', BASEPATH.'/config');

define ('CONFIG_DIR', '../config');
define ('LIBRARY_DIR', '../library/');
define ('FRAMEWORK_DIR', '../system');
define ('DEBUG', false);
//define ('DEBUG', ture);
//exit();
require_once(CONFIG_DIR. '/default.php');
include_once( FRAMEWORK_DIR. '/logging.class.php' );
use framework\log;
require_once(FRAMEWORK_DIR. '/restfull.class.php');
use framework\restfull;

try {
	$restfull = new framework\restfull\RestfullFramework($_SERVER, file_get_contents("php://input"));
	//$restfull->handle();
} catch (SoapFault $e) {
	#echo $exc->getTraceAsString();
	echo $e->getMessage();
}
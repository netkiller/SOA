<?php
include_once('../library/soaplog.class.php');
$log = new Soaplog();
print_r($log->info('Helloworld'));
print_r($log->debug('Linux'));
print_r($log->warning('hello'));
print_r($log->error('os'));

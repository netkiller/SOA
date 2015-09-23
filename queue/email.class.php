<?php
require_once 'Mail.php'; 

class Email{
	public $from = 'openunix@163.com';
	public $host 	 = '';
	public $username = '';
	public $password = '';
	
	public function __construct(){
		
	}
	public function smtp($name, $to, $subject, $text){
		$recipients = "toaddress@domain.com"; 
		
		$headers["From"] 	= sprintf("%s <%s>", $name, $to); 
		$headers["To"] 		= sprintf("%s <%s>", $name, $to); ; 
		$headers["Subject"] = $subject; 
		$headers["Reply-To"] = "reply@address.com"; 
		$headers["Content-Type"] = "text/plain; charset=UTF-8"; 
		$headers["Return-path"] = "returnpath@address.com"; 
		 
		$smtp["host"] = "smtp.server.com"; 
		$smtp["port"] = "25"; 
		$smtp["auth"] = true;
		$smtp["username"] = "smtp_user"; 
		$smtp["password"] = "smtp_password"; 

		$mail = Mail::factory("smtp", $smtp); 

		$mail->send($recipients, $headers, $text); 
	}
}

$email = new Email();
$email->smtp('Neo Chen','neo.chen@gwtsz.net','Helloworld','How are you?');
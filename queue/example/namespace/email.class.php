<?php
//require_once('PHPMailer/class.phpmailer.php');
//require_once('PHPMailer/class.smtp.php');
require 'PHPMailer/PHPMailerAutoload.php';

class Email {
	public $from = 'webmaster@example.com';
	public $host 	 = 'localhost';
	public $username = 'openunix@163.com';
	public $password = 'passw0rd';
	public $replyto	 = 'noreply@example.com';
	public $name	 = 'Webmaster';
	public $debug	 = 0;

	public function __construct(){
		//$hosts = array('202.82.201.89','202.82.201.90','202.82.201.90');
		//$this->host = $hosts[rand(0,2)];
		$this->host ="202.82.201.89";
		
	}
	public function smtp($to, $subject, $body, $option = null){
		
		if(is_array($option)){
			if(array_key_exists('name', $option)){
				$this->name = $option['name'];
			}
			if(array_key_exists('debug', $option)){
				$this->debug = $option['debug'];
			}
		}
		
		$mail             = new PHPMailer(true);
		try{
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->CharSet = 'UTF-8';
			$mail->XMailer = ' ';
			$mail->IsHTML(true);
			//$mail->SMTPSecure = 'tls';
			$mail->SMTPDebug  = $this->debug;   // enables SMTP debug information (for testing)
												// 1 = errors and messages
												// 2 = messages only
			$mail->Host       = $this->host; 	// sets the SMTP server
			$mail->Port       = 25;                    // set the SMTP port for the GMAIL server

			$mail->SMTPAuth   = false;                  // enable SMTP authentication
			$mail->Username   = $this->username; // SMTP account username
			$mail->Password   = $this->password; // SMTP account password

			$mail->SetFrom($this->from, 'Webmaster');
			$mail->AddReplyTo($this->replyto, 'Webmaster');
			
			$mail->Subject    = $subject;
			//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
			//$body             = eregi_replace("[\]",'',$body);
			$mail->MsgHTML($body);
			
			$mail->ClearAddresses();
			$mail->AddAddress($to, $this->name);

			//$mail->AddAttachment("images/phpmailer.gif");      // attachment
			//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

			if(!$mail->Send()) {
				echo "Mailer Error: " . $mail->ErrorInfo;
			}
		
		} catch (phpmailerException $e) {
			echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
			echo $e->getMessage(); //Boring error messages from anything else!
		} finally {
			$mail->smtpClose();
		}
	}
}

//$email = new Email();
//$email->smtp('Neo Chen','openunix@163.com','Helloworld','How are you?');

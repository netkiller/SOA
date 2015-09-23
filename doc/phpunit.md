PHPUnit
=====

Install PHPUnit
-----

	First of all, install phpunit.
	
	wget https://phar.phpunit.de/phpunit.phar
	chmod +x phpunit.phar
	mv phpunit.phar /usr/local/bin/phpunit
	phpunit --version
	
Create Test File
-----

	# cat hello.soap.unittest.php 
	<?php
	class HelloTest extends PHPUnit_Framework_TestCase
	{
		// ...

		public function testHello()
		{

			$options = array('uri' => "http://api.example.com",
							'location'=>'http://api.example.com/hello',
											 'compression' => 'SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP',
											'login'=>'test',
											'password'=>'passw0rd',
							'trace'=>true
											);
			$client = new SoapClient(null, $options);

			$s = null;
			
			try {
			
					$s = $client->world();
			
			}
			catch (Exception $e)
			{
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
			
			// Assert
			$this->assertEquals($s, "helloworld!!!");
		}

		// ...
	}



Test Execution
-----

	# phpunit hello.soap.unittest.php 
	PHPUnit 4.8.6 by Sebastian Bergmann and contributors.

	.

	Time: 235 ms, Memory: 13.25Mb

	OK (1 test, 1 assertion)
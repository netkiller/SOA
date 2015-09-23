Framework for RabbitMQ
=====

Install 3rdparty library
-----

### PHPMailer

	git clone https://github.com/Synchro/PHPMailer.git /srv/php/lib/php/PHPMailer

Running program
-----

	First of all, Running daemon program.
	
	# php bin/rabbitmq.php start
	
	This is daemon program, you see it by following command.
	# ps ax | grep rabbitmq.php
	23083 pts/0    S      0:00 php -d error_log=/tmp/php_errors.log -c /srv/php/etc/php-cli.ini bin/rabbitmq.php start
	


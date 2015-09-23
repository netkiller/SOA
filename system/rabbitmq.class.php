<?php

require_once( __DIR__.'/autoload.class.php' );

class RabbitMQ {
	protected $queue;
	public function __construct($exchangeName = '', $queueName = '', $routeKey = '') {
		$this->config = new \framework\Config('mq');
		$this->logfile = __DIR__.'/../log/'.__CLASS__.'.'.date('Y-m-d').'.log';
		$this->logging = new \framework\log\Logging($this->logfile); //.H:i:s

		$this->exchangeName	= $exchangeName;
		$this->queueName	= $queueName;
		$this->routeKey		= $routeKey; 

	}
	public function main(){
		
		$connection = new AMQPConnection($this->config->getArray('mq'));
		try {
			$connection->connect();
			if (!$connection->isConnected()) {
				die("Cannot connect to the broker!".PHP_EOL);
			}
			$this->channel = new AMQPChannel($connection);
			$this->exchange = new AMQPExchange($this->channel);
			$this->exchange->setName($this->exchangeName);
			$this->exchange->setType(AMQP_EX_TYPE_DIRECT); //direct类型
			$this->exchange->setFlags(AMQP_DURABLE); //持久化
			$this->exchange->declareExchange();
			//echo "Exchange Status:".$this->exchange->declare()."\n";
			//创建队列
			$this->queue = new AMQPQueue($this->channel);
			$this->queue->setName($this->queueName);
			$this->queue->setFlags(AMQP_DURABLE); //持久化
			$this->queue->declareQueue();

			$bind = $this->queue->bind($this->exchangeName, $this->routeKey);
			//echo "Message Total:".$this->queue->declare()."\n";
			//绑定交换机与队列，并指定路由键
			//echo 'Queue Bind: '.$bind."\n";
			//阻塞模式接收消息
			while(true){
				//$this->queue->consume('processMessage', AMQP_AUTOACK); //自动ACK应答
				$this->queue->consume(function($envelope, $queue) {
					//print_r($envelope);
					//print_r($queue);
					
					$msg = $envelope->getBody();
					$result = $this->loader($msg);
					$queue->ack($envelope->getDeliveryTag()); //手动发送ACK应答
	
					//$this->logging->info(''.$msg.' '.$result);
					$this->logging->debug($msg.' '.$result);
				});
				$this->channel->qos(0,1);

				//echo "Message Total:".$this->queue->declare()."\n";
			}
		}
		catch(Exception $e){
			die( $e->__toString() );
			$connection->disconnect();
		}
	}
	private function fault($tag, $msg){
		$this->logging->exception($msg);
		throw new \Exception($tag.': '.$msg);
	}
	// private
	public  function loader($msg = null){
		
		$protocol = json_decode($msg,true);

		$class = $protocol['Class'];
		$method = $protocol['Method'];
		$param = $protocol['Param'];
		$result = null;

		$classspath = __DIR__.'/../queue/'.strtolower($class)  . '.class.php';
		if( is_file($classspath) ){
			require_once($classspath);
			//$class = ucfirst(substr($request_uri, strrpos($request_uri, '/')+1));
			if (class_exists($class)) {

				if(method_exists($class, $method)){
					$obj = new $class;
					if (!$param){
						$tmp = $obj->$method();
						$result = json_encode($tmp);
						$this->logging->info($class.'->'.$method.'()');
					}else{
						$tmp = call_user_func_array(array($obj, $method), $param);
						$result = (json_encode($tmp));
						$this->logging->info($class.'->'.$method.'('.implode(",", $param).')');
					}
				}else{
					$this->logging->error('Object',$class. '->' . $method. ' is not exist.');
				}


			}else{
				$msg = sprintf("Object is not exist. (%s)", $class);
				$this->logging->error($msg);
			}
		}else{
			$msg = sprintf("Cannot loading interface! (%s)", $classspath);
			$this->logging->error($msg);
		}
		
		return $result;
	}
}

//$rabbit = new RabbitMQ($exchangeName = 'email', $queueName = 'email', $routeKey = 'email');
//$msg = json_encode(array('Class'=>'Test', 'Method'=>'sum', 'Param'=> array(1,2)));
//echo $msg;
//$rabbit->loader($msg);
//$rabbit->main();

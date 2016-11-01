<?php
namespace framework;

require_once( __DIR__.'/autoload.class.php' );

class RabbitThread extends \Thread {

	private $logging;
	private $queue;
	public $classspath;
	protected $msg;

	public function __construct($queue, $logfile, $msg) {
		$this->classspath = __DIR__.'/../queue';
		$this->msg = $msg;
		$this->queue = $queue;
		$this->logfile = $logfile;
	}
	public function run() {
		$this->logging = new \framework\log\Logging($this->logfile, $debug=true);
		$this->logging->setThreadId($this->getCurrentThreadId());
		$speed = microtime(true);
		$result = $this->loader($this->msg);
		$this->logging->debug('Time: '. (microtime(true) - $speed) .'');
	}
	// private
	public  function loader($msg = null){
		
		$protocol 	= json_decode($msg,true);
		$namespace	= $protocol['Namespace'];
		$class 		= $protocol['Class'];
		$method 	= $protocol['Method'];
		$param 		= $protocol['Param'];
		$result 	= null;

		$classspath = $this->classspath.'/'.$this->queue.'/'.$namespace.'/'.strtolower($class)  . '.class.php';
		if( is_file($classspath) ){
			require_once($classspath);
			//$class = ucfirst(substr($request_uri, strrpos($request_uri, '/')+1));
			if (class_exists($class)) {
				if(method_exists($class, $method)){
					$obj = new $class;
					if (!$param){
						$tmp = $obj->$method();
						$result = json_encode($tmp);
						$this->logging->info($class.'->'.$method.'() => '.$result);
					}else{
						$tmp = call_user_func_array(array($obj, $method), $param);
						$result = (json_encode($tmp));
						$this->logging->info($class.'->'.$method.'("'.implode('","', $param).'") => '.$result);
					}
				}else{
					$this->logging->error('Object '. $class. '->' . $method. ' is not exist.');
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

class RabbitMQ {
	
	const loop = 10;
	
	protected $queue;
	protected $pool;
	
	public function __construct($queueName = '', $exchangeName = '', $routeKey = '') {

		$this->config = new \framework\Config('rabbitmq.ini');
		$this->logfile = __DIR__.'/../log/rabbitmq.log';
		$this->logqueue = __DIR__.'/../log/queue.%s.log';
		$this->logging = new \framework\log\Logging($this->logfile, $debug=$this->config->get('pool')['debug']); //.H:i:s
		
		$this->queueName	= $queueName;
		$this->exchangeName	= $exchangeName;
		$this->routeKey		= $routeKey; 

		//$this->pool = new \Pool(8, RabbitWorker::class, []);
		$this->pool = new \Pool($this->config->get('pool')['thread']);
		$this->logging->info("Start pool: ". $this->config->get('pool')['thread']);
	}
	public function main(){
		
		$connection = new \AMQPConnection($this->config->get('rabbitmq'));
		try {
			$connection->connect();
			if (!$connection->isConnected()) {
				$this->logging->exception("Cannot connect to the broker!".PHP_EOL);
			}else{
				$this->logging->info("Connect amqp server: ". $this->config->get('rabbitmq')['host']);
			}
			$this->channel = new \AMQPChannel($connection);
			$this->channel->qos(0,1);
			
			$this->exchange = new \AMQPExchange($this->channel);
			$this->exchange->setName($this->exchangeName);
			$this->exchange->setType(AMQP_EX_TYPE_DIRECT); //direct类型
			$this->exchange->setFlags(AMQP_DURABLE); //持久�?
			$this->exchange->declareExchange();
			//echo "Exchange Status:".$this->exchange->declare()."\n";
			//创建队列
			$this->queue = new \AMQPQueue($this->channel);
			$this->queue->setName($this->queueName);
			$this->queue->setFlags(AMQP_DURABLE); //持久�?
			$this->queue->declareQueue();
			$this->queue->bind($this->exchangeName, $this->routeKey);
			//echo "Message Total:".$this->queue->declare()."\n";
			//绑定交换机与队列，并指定路由�?
			//echo 'Queue Bind: '.$bind."\n";
			//阻塞模式接收消息
			//while(true){
			//for($i=0; $i< self::loop ;$i++){
				//$this->queue->consume('processMessage', AMQP_AUTOACK); //自动ACK应答
				//echo "Message Total:".$this->queue->declare()."\n";
			//}
			$day = date("d");
			$consume = $this->queue->consume(function($envelope, $queue) use($day) {
				$msg = $envelope->getBody();
				$this->logging->debug('Protocol: '.$msg.' ');
				//$result = $this->loader($msg);
				$this->pool->submit(new RabbitThread($this->queueName, $this->logqueue, $msg));
				$queue->ack($envelope->getDeliveryTag()); //手动发ACK应答
				$now = date("d");
				if($day != $now){
					$this->logging->debug('Sharding log :'.date("Y-m-d"));
					return FALSE;
				}
			});

			$connection->disconnect();
			$this->pool->shutdown();
		}
		catch(\AMQPConnectionException $e){
			$this->logging->exception($e->__toString());
		}
		catch(\Exception $e){
			$this->logging->exception($e->__toString());
			$connection->disconnect();
			$this->pool->shutdown();
		}
	}
	private function fault($tag, $msg){
		$this->logging->exception($msg);
		throw new \Exception($tag.': '.$msg);
	}

	public function __destruct() {
	}	
}

/*
$rabbit = new RabbitMQ($queueName = 'example', $exchangeName = 'email', $routeKey = 'email');
$msg = json_encode(array('Namespace'=>'namespace', 'Class'=>'Test', 'Method'=>'sum', 'Param'=> array(1,2)));
echo $msg;
$rabbit->loader($msg);
$rabbit->main();
*/

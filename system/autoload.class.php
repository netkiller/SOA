<?php
function __autoload($className) {
	$className = strtolower($className);
	$className = basename(str_replace("\\", "/", $className ));
	//echo $className. PHP_EOL;

	if (file_exists(__DIR__.'/'. $className . '.class.php')) {
		require_once( __DIR__.'/'.$className . '.class.php' );
	}else{
		throw new Exception('Class "' . $className . '.class.php" could not be autoloaded');
	}

}
?>

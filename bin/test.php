<?php
$shortopts  = "";
$shortopts .= "t"; // These options do not accept values
$shortopts .= "c:";  // Required value
$shortopts .= "d::"; // Optional value

$longopts  = array(
    "thread",        // No value
    "connections:",     // Required value
    "daemon::",    // Optional value
    "opt",           // No value
);
$options = getopt($shortopts, $longopts);

print_r($options);
print_r($argv);

if(array_key_exists('daemon', $options) || array_key_exists('d', $options)){

	printf("Daemon!");
}

if( PHP_OS != 'Linux') {
	printf("The program for Linux!");
}
?>

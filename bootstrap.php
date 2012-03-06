<?php

conf ('Database', 'master', array (
	'driver' => 'mysql',
	'host' => $_SERVER["DB1_HOST"],
	'name' => $_SERVER["DB1_NAME"],
	'user' => $_SERVER["DB1_USER"],
	'pass' => $_SERVER["DB1_PASS"]
));

?>
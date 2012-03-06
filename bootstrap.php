<?php

conf ('Database', 'master', array (
	'driver' => 'mysql',
	'host' => $_SERVER["DB1_HOST"] . ':' . $_SERVER["DB1_PORT"] ,
	'name' => $_SERVER["DB1_NAME"],
	'user' => $_SERVER["DB1_USER"],
	'pass' => $_SERVER["DB1_PASS"]
));

// After the first run through, you can comment out or remove
// this entire block and redeploy.
if (! db_shift ('select count(*) from user')) {
	// Run the db install
	$sqldata = sql_split (file_get_contents ('conf/install_mysql.sql'));
	db_execute ('begin');
	foreach ($sqldata as $sql) {
		if (! db_execute ($sql)) {
			$err = db_error ();
			db_execute ('rollback');
			die ($err);
		}
	}

	// Create a default admin user
	$date = gmdate ('Y-m-d H:i:s');
	if (! db_execute (
		'insert into `user` (id, email, password, session_id, expires, name, type, signed_up, updated, userdata) values (1, ?, ?, null, ?, ?, "admin", ?, ?, ?)',
		$_SERVER["DEFAULT_EMAIL"],
		User::encrypt_pass ($_SERVER["DEFAULT_PASS"]),
		$date,
		'Admin User',
		$date,
		$date,
		json_encode (array ())
	)) {
		$err = db_error ();
		db_execute ('rollback');
		die ($err);
	}

	// The initial version history of the default page/blocks
	$wp = new Webpage ('index');
	Versions::add ($wp);
	$b = new Block ('members');
	Versions::add ($b);

	db_execute ('commit');
}

?>
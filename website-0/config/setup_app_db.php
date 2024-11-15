<?php
$dbCredentialsNames = [
	'DB_HOST', 'DB_NAME',
	'DB_USER', 'DB_PASS',
	'DB_ADMIN_USER', 'DB_ADMIN_PASS',
];

foreach ($dbCredentialsNames as $cred)
{
	$value = getenv($cred);
	if (empty($value))
	{
		echo "$cred is not set".PHP_EOL;
		exit(1);
	}
	define($cred, $value);
}

$mysql = new mysqli(DB_HOST, DB_ADMIN_USER, DB_ADMIN_PASS);
if ($mysql->connect_errno)
{
	echo "could not stablish connection to database: " . $mysql->error . PHP_EOL;
	exit(2);
}

$query = $mysql->prepare('CREATE DATABASE IF NOT EXISTS ?');

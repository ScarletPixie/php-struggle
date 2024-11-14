<?php

declare(strict_types=1);
define('LOGFILE', __DIR__ . '/../logs/todo_error.log');

function check_db_credentials(): void
{
	$filename = __DIR__ . '/db_credentials.php';

	if (file_exists($filename) === false || is_readable($filename) === false)
		throw new RuntimeException('cannot load db_credentials.php');

	require($filename);

	if (defined('DB_HOST') === false)
		throw new RuntimeException("DB_HOST is not set");
	if (defined('DB_NAME') === false)
		throw new RuntimeException("DB_NAME is not set");
	if (defined('DB_USER') === false)
		throw new RuntimeException("DB_USER is not set");
	if (defined('DB_PASS') === false)
		throw new RuntimeException("DB_PASS is not set");
}

try
{
	check_db_credentials();
}
catch (RuntimeException $e)
{
	error_log($e->getMessage() . PHP_EOL, 3, LOGFILE);
	http_response_code(500);
	exit();
}

?>
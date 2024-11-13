<?php

declare(strict_types=1);
session_start();
define('LOGFILE', __DIR__ . '/../logs/todo_error.log');

function login_user(string $username, string $password): bool
{
	//	stablish connection
	$mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if ($mysql->connect_errno)
		throw new RuntimeException("could not stablish connection to database " . $mysql->error);

	//	create and exec query
	$query = $mysql->prepare('SELECT username, password FROM users WHERE username = ?');
	if ($query === false)
		throw new RuntimeException("could prepate query " . $mysql->error);
	$query->bind_param('s', $username);
	if (!$query->execute())
		throw new RuntimeException("could not update database " . $mysql->error);

	$result = $query->get_result();
	if ($result->num_rows === 0)
		return false;

	//	check if passwords match
	$passwordsMatch = false;
	$resultRows = $result->fetch_assoc();
	$storedPassword = $resultRows['password'];
	if (password_verify($password, $storedPassword) === true)
	{
		session_regenerate_id(true);
		$_SESSION['id'] = $resultRows['id'];
		$_SESSION['username'] = $resultRows['username'];
		$passwordsMatch = true;
	}

	$mysql->close();
	$query->close();
	return $passwordsMatch;
}

function register_user(string $username, string $password): void
{
	//	stablish connection
	$mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if ($mysql->connect_errno)
		throw new RuntimeException("could not stablish connection to database " . $mysql->error);

	//	create query
	$query = $mysql->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
	if ($query === false)
		throw new RuntimeException("could prepate query " . $mysql->error);
	
	$hashed_password = password_hash($password, PASSWORD_BCRYPT);
	$query->bind_param("ss", $username, $hashed_password);

	//	updata database
	if (!$query->execute())
		throw new RuntimeException("could not update database " . $mysql->error);

	//	close connection
	$query->close();
	$mysql->close();
}

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
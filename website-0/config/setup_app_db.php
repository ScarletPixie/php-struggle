<?php
declare(strict_types=1);

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


$db_name = DB_NAME;
$db_host = DB_HOST;
$db_user = DB_USER;
$db_pass = DB_PASS;

//	CREATE DATABASE
$query = xquery_prep($mysql, "CREATE DATABASE IF NOT EXISTS `?`");
$query->bind_param("s", $db_name);
xquery_exec($mysql, $query);
$query->close();


//	CREATE TABLES
//		create users table
$mysql->select_db($db_name);
$query = xquery_prep($mysql, "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(255) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL)");
xquery_exec($mysql, $query);
$query->close();
//		create tasks table
$query = xquery_prep($mysql, "CREATE TABLE IF NOT EXISTS tasks (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, description TEXT, user_id INT, FOREIGN KEY (user_id) REFERENCES users(id))");
xquery_exec($mysql, $query);
$query->close();


//	CREATE DB USER
//		create user
$query = xquery_prep($mysql, "CREATE USER IF NOT EXISTS '?'@'?' IDENTIFIED BY '?'");
$query->bind_param('sss', $db_user, $db_host, $db_pass);
xquery_exec($mysql, $query);
$query->close();
//		give permissions on db
$query = xquery_prep($mysql, "GRANT ALL PRIVILEGES ON `?`.* TO '?'@'?'");
$query->bind_param('sss', $db_name, $db_user, $db_host);
xquery_exec($mysql, $query);
$query->close();
//		flush permissions
$query = xquery_prep($mysql, 'FLUSH PRIVILEGES');
xquery_exec($mysql, $query);
$query->close();


$mysql->close();

//	HELPER FUNCTIONS
function xquery_exec(mysqli $conn, mysqli_stmt $stmt): void
{
	if (!$stmt->execute())
		die("cannot execute query: ".$conn->error.PHP_EOL);
}
function xquery_prep(mysqli $conn, string $query): mysqli_stmt
{
	$stmt = $conn->prepare($query);
	if ($stmt === false)
		die("cannot prepare query: ".$conn->error.PHP_EOL);
	return $stmt;
}

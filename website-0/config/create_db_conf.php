<?php
$dbCredFile = __DIR__ . '/db_credentials.php';
if (file_exists($dbCredFile))
{
	echo "db_credentials.php already exists!".PHP_EOL;
	exit(1);
}

$configKeys = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
$config = [];

foreach ($configKeys as $key)
{
	$value = getenv($key);
	if (empty($value))
	{
		echo "cannot create db_credentials.php file: $key is not set".PHP_EOL;
		exit(2);
	}
	$config[$key] = $value;
}

$fileContent = ['<?php'];
foreach ($config as $key => $value)
	$fileContent[] = "define('$key', '$value');";

if (file_put_contents($dbCredFile, join(PHP_EOL, $fileContent)) === false)
{
	echo "failed to create db_credentials.php file" . PHP_EOL;
	exit(3);
}

echo "successfully created db_credentials.php file".PHP_EOL;
?>

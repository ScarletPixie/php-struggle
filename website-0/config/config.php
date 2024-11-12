<?php

declare(strict_types=1);

function loadEnv(string $filePath): void
{
	if (!file_exists($filePath))
		throw new RuntimeException("Environment file not found at: $filePath");;

	$lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach ($lines as $line)
	{
		$entry = explode('=', $line);
		if (strpos(trim($line), '#') === 0)
			continue;

		define($entry[0], $entry[1]);
	}

	if (!defined('PHP_DB_HOST'))
		throw new RuntimeException(nl2br("Missing PHP_DB_HOST entry\n"));
	if (!defined('PHP_DB_NAME'))
		throw new RuntimeException(nl2br("Missing PHP_DB_NAME entry\n"));
	if (!defined('PHP_DB_USER'))
		throw new RuntimeException(nl2br("Missing PHP_DB_USER entry\n"));
	if (!defined('PHP_DB_PASS'))
		throw new RuntimeException(nl2br("Missing PHP_DB_PASS entry\n"));
}

try
{
	loadEnv(__DIR__ . '/.env');
}
catch (Exception $e)
{
	echo "fatal error: ". $e->getMessage();
}

?>
<?php
declare(strict_types=1);

function login_user(string $username, string $password): bool
{
	//	stablish connection
	$mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if ($mysql->connect_errno)
		throw new RuntimeException("could not stablish connection to database: " . $mysql->error);

	//	create and exec query
	$query = $mysql->prepare('SELECT username, password FROM users WHERE username = ?');
	if ($query === false)
		throw new RuntimeException("could prepate query: " . $mysql->error);
	$query->bind_param('s', $username);
	if (!$query->execute())
	{
		$query->close();
		throw new RuntimeException("could not update database: " . $mysql->error);
	}

	$result = $query->get_result();
	if ($result->num_rows === 0)
	{
		$query->close();
		return false;
	}

	//	check if passwords match
	$validLogin = false;
	$resultRows = $result->fetch_assoc();
	$storedPassword = $resultRows['password'];
	if (password_verify($password, $storedPassword) === true)
	{
		session_regenerate_id(true);
		$_SESSION['username'] = $resultRows['username'];
		$validLogin = true;
	}

	$mysql->close();
	$query->close();
	return $validLogin;
}

function register_user(string $username, string $password): bool
{
	//	stablish connection
	$mysql = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
	if ($mysql->connect_errno)
		throw new RuntimeException("could not stablish connection to database " . $mysql->error);

	//	check if user already exists
	$exists = $mysql->prepare("SELECT id FROM users WHERE username = ?");
	if ($exists === false)
		throw new RuntimeException("could prepate query " . $mysql->error);

	$exists->bind_param('s', $username);
	if (!$exists->execute())
		throw new RuntimeException("could check database " . $mysql->error);
	$user = $exists->get_result();
	if ($user->num_rows > 0)
	{
		$exists->close();
		return false;
	}
	$exists->close();

	//	create query
	$query = $mysql->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
	if ($query === false)
		throw new RuntimeException("could prepate query " . $mysql->error);
	
	$hashed_password = password_hash($password, PASSWORD_BCRYPT);
	$query->bind_param("ss", $username, $hashed_password);

	//	updata database
	if (!$query->execute())
	{
		$query->close();
		throw new RuntimeException("could not update database " . $mysql->error);
	}

	//	close connection
	$query->close();
	$mysql->close();
	return true;
}

function validate_form_input(string $name, string $pass): array
{
	$errors = [];

	if (strlen($name) < 3 || strlen($pass) < 3)
		$errors[] = 'username/password must at least 3 characters long';
	else if (preg_match('/[^a-zA-Z]/', $name))
		$errors[] = 'only alphabetic characters allowed in username';
	else if (preg_match('/\s/', $pass))
		$errors[] = 'password cannot contain spaces';

	if (htmlspecialchars($name) !== $name || htmlspecialchars($pass) !== $pass)
		$errors[] = 'username/password contains invalid characters';

	return $errors;
}
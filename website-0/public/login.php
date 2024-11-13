<?php
	if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		if (isset($_POST["username"]) === false || isset($_POST["password"]) === false)
		{
			echo "please enter name and password";
			return;
		}

		$name = trim($_POST["username"]);
		$pass = $_POST["password"];
	
		if (strlen($name < 3) || strlen($pass) < 3)
		{
			echo "name/pass must be at least 3 characters long";
			return;
		}
		if (preg_match('/[^a-zA-Z]/', $name))
		{
			echo "name can only contain letters";
			return;
		}
		if (preg_match('/\s/', $pass))
		{
			echo "password cannot contain spaces";
			return;
		}

		if (htmlspecialchars($name) !== $name || htmlspecialchars($pass !== $pass))
		{
			echo "invalid chars!";
			return;
		}

		require(__DIR__ . '/../config/config.php');

		try
		{
			if (login_user($name, $pass) === false)
				echo "invalid user/password";
			else
			{
				header("Location: index.php");
				exit;
			}
				
		}
		catch (Exception $e)
		{
			error_log($e->getMessage() . PHP_EOL, 3, LOGFILE);
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>registration</title>
</head>
<body>
	<h1>LOGIN PAGE</h1>
	<form action="login.php" method="post">
		<input type="text" name="username">
		<input type="password" name="password">
		<button type="submit" name="submit">Submit</button>
	</form>
</body>
</html>
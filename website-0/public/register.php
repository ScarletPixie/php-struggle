<?php
	session_start();
	if (!isset($_SESSION['csrf_token']))
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

	if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		$errors = [];
		//	missing token or mismatched
		if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || ($_SESSION['csrf_token'] !== $_POST['csrf_token']))
		{
			echo "Invalid form!";
			exit(0);
		}

		try
		{
			require(__DIR__ . '/../src/auth.php');
			require(__DIR__ . '/../config/config.php');

			if (isset($_POST["username"]) === false || isset($_POST["password"]) === false)
				$errors[] = "please enter name and password";
			else if (empty($errors))
			{
				$errors = validate_form_input($_POST["username"], $_POST["password"]);
				if (empty($errors))
				{
					if (register_user($_POST["username"], $_POST["password"]))
					{
						header("Location: login.php");
						echo "successfuly registered user: " . '<a href="login.php">go to login page</a>';
						exit(0);
					}
					$errors[] = "user already exists";
				}
			}
		}
		catch (Exception $e)
		{
			error_log($e->getMessage() . PHP_EOL, 3, LOGFILE);
			echo "fatal error, try again later";
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
	<H1>REGISTRATION PAGE</H1>
	<form action="register.php" method="post">
		<input type="text" name="username">
		<input type="password" name="password">
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
		<button type="submit" name="submit">Submit</button>
		<div>
			<?php echo join("<br>", $errors) ?>
		</div>
	</form>
</body>
</html>
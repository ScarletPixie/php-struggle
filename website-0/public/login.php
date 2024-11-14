<?php
	$errors = [];
	session_start();
	if (!isset($_SESSION['csrf_token']))
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

	if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
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
			else if (count($errors) === 0)
			{
				$errors = validate_form_input($_POST["username"], $_POST["password"]);
				if (count($errors) === 0)
				{
					if (login_user($_POST["username"], $_POST["password"]))
					{
						header("Location: index.php");
						echo "successfuly registered user: " . '<a href="index.php">go to index page</a>';
						exit(0);
					}
					$errors[] = "invalid username/password";
				}
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
		<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
		<button type="submit" name="submit">Submit</button>
		<div>
			<?php
				echo((count($errors) === 0) ? '' : join("", $errors));
			?>
		</div>
	</form>
</body>
</html>
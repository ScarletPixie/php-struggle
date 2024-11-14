<?php
	session_start();
	if (isset($_SESSION['username']) === false)
	{
		header("Location: login.php");
		echo '<a href="login.php">go to login page</a>';
		exit();
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>hello <?php echo $_SESSION['username']?></title>
</head>
<body>
	<h1>hello <?php echo $_SESSION['username']?></h1>
	<form action="logout.php" method="post">
		<button type="submit">logout</button>
	</form>
	<form action="create_task.php" method="post">
		
	</form>
</body>
</html>
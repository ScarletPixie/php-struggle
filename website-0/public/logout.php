<?php

session_start();
session_unset();
session_destroy();
header("Location: login.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="refresh" content="2;url=login.php">
	<title>logout</title>
</head>
<body>
	<p>
		you'll be redirected automatically <a href="login.php">click here to go back to login page</a>
	</p>
</body>
</html>
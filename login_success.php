<?php
session_start();

//If no user is logged in, setLoggedInUser to None
if (!isset($_SESSION["loggedInUser"])){
    $_SESSION["loggedInUser"] = "None";
}

?>

<html>
<head>
<title>Register account</title>
<link rel="stylesheet" type="text/css" href="default.css">
</head>

<body>
<center>

<div class="header">
    <h1><a href="login.php">Log in</a></h1>
</div>

<div class="content">
    Login successful.<br><br>
    Current user: <?php echo $_SESSION["loggedInUser"] ?>
</div>

</center>
</body>
</html>
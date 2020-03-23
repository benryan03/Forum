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
    <h1><a href="index.php">Forum</a></h1>
</div>

<div class="options">
    <a href="register.php">Register</a>&nbsp;
    <a href="login.php">Log in</a>&nbsp;
    <a href="new_thread.php">New thread</a>&nbsp;
    Current user: <?php echo $_SESSION["loggedInUser"] ?>&nbsp;
    <a href="logout.php">Log out</a>
</div>

<div class="content">
    Account successfully created.<br><br>
    <a href="login.php">Log in</a>
</div>

</center>
</body>
</html>
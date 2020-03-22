<?php
session_start();
$_SESSION["loggedInUser"] = "None";
?>

<html>
<head>
    <title>Forum</title>
    <link rel="stylesheet" type="text/css" href="default.css">
</head>
<body>
    <center>
    <div class="header">
    <h1><a href="index.php">Forum</a></h1>
    </div>

    <div class="content">
    <a href="register.php">Register</a>&nbsp;
    <a href="login.php">Log in</a>&nbsp;
    Current user: <?php echo $_SESSION["loggedInUser"] ?>&nbsp;
    Log out<br><br>

    You have been sucessfully logged out.

    </div>


    </center>
</body>
</html>
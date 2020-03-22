<?php
session_start();
?>

<html>
<head>
    <title>Welcome to Forum</title>
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
    <a href="logout.php">Log out</a>
    <br><br>
    Some content
    </div>


    </center>
</body>
</html>
<?php
session_start();
$_SESSION["loggedInUser"] = "None";

//If no user is logged in, setLoggedInUser to None
if (!isset($_SESSION["loggedInUser"])){
    $_SESSION["loggedInUser"] = "None";
}

$loggedInUser = $_SESSION["loggedInUser"];
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

    <div class="options">
        <?php if ($loggedInUser == "None"){echo '<a href="register.php">Register</a>&nbsp;';} ?>
        <?php if ($loggedInUser == "None"){echo '<a href="login.php">Log in</a>&nbsp;';} ?>
        <?php if ($loggedInUser != "None"){echo '<a href="new_thread.php">New thread</a>&nbsp;';} ?>
        Current user: <?php echo $loggedInUser ?>&nbsp;
        <?php if ($loggedInUser != "None"){echo '<a href="logout.php">Log out</a>';} ?>
    </div>

    <div class="content">
        You have been sucessfully logged out.
    </div>


    </center>
</body>
</html>
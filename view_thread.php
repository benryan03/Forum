<?php

//If no user is logged in, setLoggedInUser to None
if (!isset($_SESSION["loggedInUser"])){
    $_SESSION["loggedInUser"] = "None";
}

//Get thread ID from previous link
$thread_id = $_GET['thread_id'];

date_default_timezone_set("America/New_York");
$timestamp = date("Y/m/d h:i:sa");

//Connect to database
$serverName = "localhost\sqlexpress";
$connectionInfo = array("Database"=>"Forum", "UID"=>"ben", "PWD"=>"password123");
$conn = sqlsrv_connect($serverName, $connectionInfo);

//Query selected thread from database            
$query = "SELECT * FROM threads WHERE thread_id = '$thread_id' ";
$thread_array = sqlsrv_query($conn, $query, array());
$thread_array = sqlsrv_fetch_array($thread_array); //Convert result to array

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
    <?php 
    
    echo nl2br("View thread ID: ".$thread_id."\n\n");

    echo nl2br(
        "TITLE: ".$thread_array[2]."\n\n".
        "CONTENT: ".$thread_array[3]."\n\n".
        "REPLIES: ".$thread_array[5]."\n\n".
        "AUTHOR: ".$thread_array[4]."\n\n".
        "SUBMITTED AT: ".date_format($thread_array[6], "Y/m/d h:i:sa")."\n\n".
        "UPDATED AT: ".date_format($thread_array[7], "Y/m/d h:i:sa")
    );

    ?>
</div>

</center>
</body>
</html>
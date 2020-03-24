<?php
    session_start();

    //Get selectedUser from previous link
    $selectedUser = $_GET['selectedUser'];

    date_default_timezone_set("America/New_York");
    $timestamp = date("m/d/Y h:ia");
?>

<html>
<head>
    <title>View user</title>
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
        <?php echo "Viewing profile of: ".$selectedUser; ?><br>
        <?php

            //Connect to database
            $serverName = "localhost\sqlexpress";
            $connectionInfo = array("Database"=>"Forum", "UID"=>"ben", "PWD"=>"password123");
            $conn = sqlsrv_connect($serverName, $connectionInfo);

            //Query database for number of threads posted by user
            $query = "SELECT * FROM threads WHERE author = '$selectedUser'";
            $result = sqlsrv_query($conn, $query, array(), array( "Scrollable" => 'static'));
            $thread_count = sqlsrv_num_rows($result);

            //Query database for number of comments posted by user
            $query = "SELECT * FROM posts WHERE post_author = '$selectedUser'";
            $result = sqlsrv_query($conn, $query, array(), array( "Scrollable" => 'static'));
            $comment_count = sqlsrv_num_rows($result);

            echo nl2br("User has created ".$thread_count." threads.\n");
            echo nl2br("User has posted ".$comment_count." comments.\n");
    
    
    
    
    
    
    
    
    
        ?>
    </div>

    </center>
</body>
</html>
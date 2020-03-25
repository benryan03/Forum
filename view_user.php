<?php
    session_start();

    //Get selectedUser from previous link
    $selectedUser = $_GET['selectedUser'];

    date_default_timezone_set("America/New_York");
    $timestamp = date("m/d/Y h:ia");

    //Connect to database
    $serverName = "localhost\sqlexpress";
    $connectionInfo = array("Database"=>"Forum", "UID"=>"ben", "PWD"=>"password123");
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    //Query database for number of threads posted by user
    $query = "SELECT * FROM threads WHERE author = '$selectedUser'";
    $threadsArray = sqlsrv_query($conn, $query, array(), array( "Scrollable" => 'static'));
    $thread_count = sqlsrv_num_rows($threadsArray);

    //Query database for number of comments posted by user
    $query = "SELECT * FROM posts WHERE post_author = '$selectedUser'";
    $commentsArray = sqlsrv_query($conn, $query, array(), array( "Scrollable" => 'static'));
    $comment_count = sqlsrv_num_rows($commentsArray);
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
        <?php
            echo nl2br("Viewing profile of: ".$selectedUser."\n");
            echo nl2br("User has created ".$thread_count." threads.\n");
            echo nl2br("User has posted ".$comment_count." comments.");
        ?>
    </div>
    <div class="view_user_threads">
        Threads:<br>
        <?php

            //Display threads
            for ($x = 1; $x < $thread_count + 1; $x++){

                $thread_array_row = sqlsrv_fetch_array($threadsArray, SQLSRV_FETCH_NUMERIC); //Select next row
                
                //If thread title will overflow its space, shorten it
                $threadTitle = trim($thread_array_row[2]); //Remove whitespace from beginning and end of array item
                if ( strlen($threadTitle) > 42) {
                    $threadTitle = substr($threadTitle, 0, 42)."...";
                }

                echo nl2br(
                    //Do not change the next 2 lines or all formatting will break :)
                    '<div class="complete_thread"><span class="thread_title">'."<a href='view_thread.php?thread_id=$thread_array_row[0]'>$threadTitle</a></span><span class='thread_details'>replies: $thread_array_row[5] by: <a href='view_user.php?selectedUser=$thread_array_row[4]'>$thread_array_row[4]</a></span>
                    </div>"
                );    
            }
        ?>
    </div>
    <div class="view_user_comments">
        Comments:<br>
        <?php
            //Display comments
            for ($x = 1; $x < $comment_count + 1; $x++){
                $comment_array_row = sqlsrv_fetch_array($commentsArray, SQLSRV_FETCH_NUMERIC); //Select next row                

                echo nl2br(
                    "<h2><i>post id:".$comment_array_row[0].
                    " | submitted: ".date_format($comment_array_row[4], "m/d/Y h:ia")."</i></h2>".
                    $comment_array_row[2]. //Comment text
                    "\n\n"
                );
            }
        ?>
    </div>

    </center>
</body>
</html>
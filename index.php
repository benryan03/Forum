<?php
session_start();

$x = "";
date_default_timezone_set("America/New_York");
//$timestamp = date("Y/m/d h:i:sa");

//If no user is logged in, setLoggedInUser to None
if (!isset($_SESSION["loggedInUser"])){
    $_SESSION["loggedInUser"] = "None";
}

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

    <div class="options">
        <a href="register.php">Register</a>&nbsp;
        <a href="login.php">Log in</a>&nbsp;
        <a href="new_thread.php">New thread</a>&nbsp;
        Current user: <?php echo $_SESSION["loggedInUser"] ?>&nbsp;
        <a href="logout.php">Log out</a>
    </div>

    <div class="threads">
        <?php
            //Connect to database
            $serverName = "localhost\sqlexpress";
            $connectionInfo = array("Database"=>"Forum", "UID"=>"ben", "PWD"=>"password123");
            $conn = sqlsrv_connect($serverName, $connectionInfo);

            //Query database for number of total threads
            $query = "SELECT * FROM threads";
            $result = sqlsrv_query($conn, $query, array(), array( "Scrollable" => 'static'));
            $thread_count = sqlsrv_num_rows($result);
            echo nl2br("Total threads: ".$thread_count."\n\n");

            for ($x = 1; $x < $thread_count + 1; $x++){
                $query = "SELECT * FROM threads WHERE thread_id = '$x' ";
                $thread_array = sqlsrv_query($conn, $query, array());
                $thread_array = sqlsrv_fetch_array($thread_array); //Convert result to array
                
                echo nl2br(
                    //Do not change the next 2 lines or all formatting will break :)
                    '<div class="complete_thread"><span class="thread_title">'."<a href='view_thread.php?thread_id=$thread_array[0]'>$thread_array[2]</a></span><span class='thread_details'>replies: $thread_array[5] by: <a href='view_user.php?selectedUser=$thread_array[4]'>$thread_array[4]</a></span>
                    </div>"
                );
                
                //echo nl2br("TITLE: ".$thread_array[2]." TEXT: ".$thread_array[3]." REPLIES: ".$thread_array[5]." UPDATED: ".date_format($thread_array[6], "Y/m/d h:i:sa")."\n\n");
            }
            print_r(sqlsrv_errors());
        ?>
    </div>


    </center>
</body>
</html>
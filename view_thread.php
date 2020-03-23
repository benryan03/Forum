 <?php
    session_start();

    date_default_timezone_set("America/New_York");
    $timestamp = date("Y/m/d h:i:sa");
    $comment_text = "";

    //If no user is logged in, setLoggedInUser to None
    if (!isset($_SESSION["loggedInUser"])){
        $_SESSION["loggedInUser"] = "None";
    }

    //Get thread ID from previous link
    $thread_id = $_GET['thread_id'];

    //Connect to database
    $serverName = "localhost\sqlexpress";
    $connectionInfo = array("Database"=>"Forum", "UID"=>"ben", "PWD"=>"password123");
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    //Query selected thread from database            
    $query = "SELECT * FROM threads WHERE thread_id = '$thread_id' ";
    $thread_array = sqlsrv_query($conn, $query, array());
    $thread_array = sqlsrv_fetch_array($thread_array); //Convert result to array

    if (!empty($_POST['comment_text'])){
        $comment_text = $_POST['comment_text'];
    }
    if ($comment_text != ""){
        //Write comment to database
            
        //To calculate new comment ID, count number of rows in database and add 1
        $countExistingPostsQuery = "SELECT * FROM posts";
        $countExistingPosts = sqlsrv_query($conn, $countExistingPostsQuery, array(), array( "Scrollable" => 'static' ));
        $posts_count = sqlsrv_num_rows( $countExistingPosts );
        $newPostID = $posts_count + 1;

        //Write new comment to database
        $newPostQuery = "INSERT INTO posts VALUES ('$newPostID', '$thread_id', '$comment_text', '$_SESSION[loggedInUser]', '$timestamp', '$timestamp')";
        $writeToDatabase = sqlsrv_query($conn, $newPostQuery);

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

    <div class="op">
        <?php     
            //Display thread OP
            echo nl2br(
                "<h2>TITLE: ".$thread_array[2].
                " REPLIES: ".$thread_array[5].
                " AUTHOR: ".$thread_array[4]."\n".
                "SUBMITTED AT: ".date_format($thread_array[6], "Y/m/d h:i:sa").
                " UPDATED AT: ".date_format($thread_array[7], "Y/m/d h:i:sa\n").
                "</h2>CONTENT: ".$thread_array[3]."\n\n"
            );
        ?>
    </div>





    
    <div class="comments">
        <?php
            //Display comments

            //Count how many comments are in the the thread
            $query = "SELECT * FROM posts WHERE thread_id = '$thread_id' ORDER BY date_submitted";
            $comments_array = sqlsrv_query($conn, $query, array(), array( "Scrollable" => 'static'));
            $posts_count = sqlsrv_num_rows($comments_array);
            
            //Display each comment
            for ($x = 1; $x < $posts_count + 1; $x++){
                $comment_array_row = sqlsrv_fetch_array($comments_array, SQLSRV_FETCH_NUMERIC); //Select next row                

                echo nl2br(
                    "<h2>POST ID:".$comment_array_row[0].
                    " AUTHOR: ".$comment_array_row[3].
                    " SUBMITTED AT: ".date_format($comment_array_row[4], "Y/m/d h:i:sa")."</h2>".
                    $comment_array_row[2].
                    "\n\n"
                );
            }
        ?><br><br>

        <form action="?thread_id=<?php echo $thread_id ?>&<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
            <textarea name="comment_text" rows="4" cols="50" placeholder="Add comment" value="<?php echo htmlentities($thread_text) ?>"></textarea><br><br>
            <input type="submit" value="Submit" name="submit"><br>        
        </form>
    </div>

    </center>
</body>
</html>
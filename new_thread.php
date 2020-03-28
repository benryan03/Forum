<?php
session_start();

//If no user is logged in, setLoggedInUser to None
if (!isset($_SESSION["loggedInUser"])){
    $_SESSION["loggedInUser"] = "None";
}

$thread_title = "";
$thread_text = "";
$title_error = "";
$text_error = "";
date_default_timezone_set("America/New_York");
$timestamp = date("m/d/Y h:ia");
$errorStatus = false;

$newPostID = 1; //I will change this later

if (!empty($_POST["submit"])){

    //Retreive and sanitize submitted thread title and text
    $thread_title = htmlspecialchars($_POST["thread_title"]);
    $thread_text = htmlspecialchars($_POST["thread_text"]);

    //Validate thread title and text
    if ($thread_title == ""){
        $title_error = "Error: Title cannot be empty";
        $errorStatus = true;
    }
    if (strlen($thread_title) > 50){
        $title_error = "Error: Maximum length 50 characters (current: ".strlen($thread_title).")";
        $errorStatus = true;
    }
    if ($thread_text == ""){
        $text_error = "Error: Text cannot be empty";
        $errorStatus = true;
    }
    if (strlen($thread_text) > 1000){
        $text_error = "Error: Maximum length 1000 characters (current: ".strlen($thread_text).")";
        $errorStatus = true;
    }
   
    if ($errorStatus == false) { //No errors - submit to database

        //Connect to database
        $serverName = "localhost\sqlexpress";
        $connectionInfo = array("Database"=>"Forum", "UID"=>"ben", "PWD"=>"password123");
        $conn = sqlsrv_connect($serverName, $connectionInfo);

        //To calculate new thread ID, count number of rows in database and add 1
        $countExistingThreadsQuery = "SELECT * FROM threads";
        $countExistingThreads = sqlsrv_query($conn, $countExistingThreadsQuery, array(), array( "Scrollable" => 'static' ));
        $threads_count = sqlsrv_num_rows( $countExistingThreads );
        $newThreadID = $threads_count + 1;

        //Write new thread to database
        $userRegisterQuery = "INSERT INTO threads VALUES ('$newThreadID', '$newPostID', '$thread_title', '$thread_text', '$_SESSION[loggedInUser]', '0', '$timestamp', '$timestamp')";
        $writeToDatabase = sqlsrv_query($conn, $userRegisterQuery);

        //Open success page
        header("Location:view_thread.php?thread_id=$newThreadID");
    }
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

    <div class="content">
        New thread<br><br>
        <form action="? echo $_SERVER["PHP_SELF"]" method="post">

        <input type="text" name="thread_title" placeholder="Thread title" value="<?php echo $thread_title ?>"><br>
        <div class="error" id="title_error"><?php echo ($title_error) ?></div><br>

        <textarea name="thread_text" rows="4" cols="50" placeholder="Thread text"><?php echo $thread_text ?></textarea><br>
        <div class="error" id="text_error"><?php echo ($text_error) ?></div><br>

        <input type="submit" value="Submit" name="submit"><br>        
        
        </form>
    </div>

    </center>
</body>
</html>
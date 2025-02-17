<?php
    session_start();

    //If no user is logged in, setLoggedInUser to None
    if (!isset($_SESSION["loggedInUser"])){
        $_SESSION["loggedInUser"] = "None";}

    $loggedInUser = $_SESSION["loggedInUser"];

    date_default_timezone_set("America/New_York");
    $timestamp = date("m/d/Y h:ia");

    $comment_text = "";
    $comment_error = ""; //new comment error
    $edit_comment_error = "";
    $errorStatus = null;

    $edit_op_text = "";
    $op_error = " ";
    $opErrorStatus = null;

    //Get thread ID from previous link
    $thread_id = $_GET['thread_id'];

    //Get edited post ID from previous link, if it exists
    if (isset($_GET['edited_post_id'])){
        $edited_post_id = $_GET['edited_post_id'];}
    else{
        $edited_post_id = "";
    }

    //Connect to database
    $serverName = "localhost\sqlexpress";
    $connectionInfo = array("Database"=>"Forum", "UID"=>"ben", "PWD"=>"password123");
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    if (!empty($_POST['submit'])){//When new comment is submitted

        //Retrieve and sanitize comment
        $comment_text = htmlspecialchars($_POST['comment_text']);

        //Validate comment
        if ($comment_text == ""){
            $comment_error = "Error: Comment cannot be empty";
            $errorStatus = true;}
        if (strlen($comment_text) > 1000){
            $comment_error = "Error: Maximum length 1000 characters (current: ".strlen($comment_text).")";
            $errorStatus = true;}

        if ($errorStatus == false){ //Write comment to database

            //To calculate new comment ID, count number of rows in database and add 1
            $countExistingPostsQuery = "SELECT * FROM posts";
            $countExistingPosts = sqlsrv_query($conn, $countExistingPostsQuery, array(), array( "Scrollable" => 'static' ));
            $posts_count = sqlsrv_num_rows( $countExistingPosts );
            $newPostID = $posts_count + 1;
    
            //Write new comment to database
            $newPostQuery = "INSERT INTO posts VALUES ('$newPostID', '$thread_id', '$comment_text', '$_SESSION[loggedInUser]', '$timestamp', '$timestamp', '0')";
            $writeToDatabase = sqlsrv_query($conn, $newPostQuery);
    
            //Query number of existing comments (for thread comment count number in index.php)
            $replyCountQuery = "SELECT reply_count FROM threads WHERE thread_id = '$thread_id' ";
            $reply_count = sqlsrv_query($conn, $replyCountQuery, array());
            $reply_count = sqlsrv_fetch_array($reply_count);
            $reply_count = $reply_count[0];
    
            //Update comment count and date updated for thread
            $reply_count = $reply_count + 1;
            $threadUpdateQuery = "UPDATE threads SET reply_count = '$reply_count', time_updated = '$timestamp' WHERE thread_id = '$thread_id' ";
            $writeToDatabase = sqlsrv_query($conn, $threadUpdateQuery);
        }
    }

    if (!empty($_POST['submit_edit_op'])){ //When OP edit is submitted

        //Retrieve and sanitize the edit
        $op_text = htmlspecialchars($_POST['edit_op_text']);

        //Validate edit
        if ($op_text == ""){
            $op_error = "Error: Post cannot be empty";
            $opErrorStatus = true;}
        if (strlen($op_text) > 1000){
            $op_error = "Error: Maximum length 1000 characters (current: ".strlen($op_text).")";
            $opErrorStatus = true;}

        if ($opErrorStatus == null){ //Write edit to database
            $editOPQuery = "UPDATE threads SET op_text = '$op_text', time_updated = '$timestamp', edited_status = '1' WHERE thread_id = '$thread_id'";
            $writeToDatabase = sqlsrv_query($conn, $editOPQuery);
            $opErrorStatus = false;}
    }

    if (!empty($_POST['submit_edit'])){ //When comment edit is submitted

        //Retrieve and sanitize the edit
        $edit_text = htmlspecialchars($_POST['edit_text']);

        //Validate edit
        if ($edit_text == ""){
            $edit_comment_error = "Error: Comment cannot be empty";
            $errorStatus = true;}
        if (strlen($edit_text) > 1000){
            $edit_comment_error = "Error: Maximum length 1000 characters (current: ".strlen($edit_text).")";
            $errorStatus = true;}

        if ($errorStatus == false){ //Write edit to database
            $editPostQuery = "UPDATE posts SET post_text = '$edit_text', date_updated = '$timestamp', edited_status = '1' WHERE post_id = '$edited_post_id'";
            $writeToDatabase = sqlsrv_query($conn, $editPostQuery);}
    }
?>

<html>
<head>
<title>View thread</title>
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

    <div class="op">
        <?php
            //Query selected thread from database            
            $query = "SELECT * FROM threads WHERE thread_id = '$thread_id' "; //Query updated OP        
            $thread_array = sqlsrv_query($conn, $query, array());
            $thread_array = sqlsrv_fetch_array($thread_array); //Convert result to array   
            $op_text = $thread_array[3];

            //Define edit link for OP
            $editLink = "";
            if ($loggedInUser == trim($thread_array[4])){
                $editLink = " | <a href='?thread_id=$thread_id&editOPClicked'>Edit</a>";}

            //Display thread OP
            echo nl2br(
                "<h2>".$thread_array[2].
                "<i> | ".$thread_array[5]." replies |".
                " by: <a href='view_user.php?selectedUser=$thread_array[4]'>".trim($thread_array[4])."</a>"." | ".
                "submitted: ".date_format($thread_array[6], "m/d/Y h:ia").
                $editLink.
                "</i>\n");

            //If OP has been edited, display datetime of update
            if ($thread_array[8] == "1"){
                echo "<b>Edited at ".date_format($thread_array[7], "m/d/Y h:ia")."</b></h2>";}
            else{
                echo "</h2>";}

            //If logged in user is the author of the post (protects against URL editing)
            //and edit link has been clicked, display text box to edit OP
            if ($loggedInUser == trim($thread_array[4]) && isset($_GET['editOPClicked']) || $opErrorStatus == true){   
                
                //If comment did not pass validation, keep it in text box
                if (isset($_POST['edit_op_text'])){
                    $op_text = $_POST['edit_op_text'];}

                echo nl2br(
                    '<form action="?thread_id='.$thread_id.'" method="post">'.
                    '<textarea name="edit_op_text" rows="4" cols="50" >'.
                    htmlspecialchars_decode(trim($op_text)).'</textarea><br>'.
                    '<div class="error" id="op_error">'.$op_error.'</div><br>'.
                    '<input type="submit" value="Submit" name="submit_edit_op">'.
                    '</form>');
            }
            else{
                echo nl2br(trim($thread_array[3])."\n\n");
            }   
        ?>
    </div>
 
    <div class="comments">
        <?php
            //Count how many comments are in the the thread
            $query = "SELECT * FROM posts WHERE thread_id = '$thread_id' ORDER BY date_submitted";
            $comments_array = sqlsrv_query($conn, $query, array(), array( "Scrollable" => 'static'));
            $posts_count = sqlsrv_num_rows($comments_array);
            
            //Display each comment
            for ($x = 1; $x < $posts_count + 1; $x++){
                $comment_array_row = sqlsrv_fetch_array($comments_array, SQLSRV_FETCH_NUMERIC); //Select next row                
                $edit_text = $comment_array_row[2];

                //Define edit link for each comment, with individual comment ID
                $editLink = "";
                if ($loggedInUser == trim($comment_array_row[3])){
                    $editLink = " | <a href='?thread_id=$thread_id&editClicked&edited_post_id=$comment_array_row[0]'>Edit</a>";}

                //Display comment metadata
                echo nl2br(
                    "<h2><i>post id:".$comment_array_row[0].
                    " | by: <a href='view_user.php?selectedUser=$thread_array[4]'>".trim($comment_array_row[3])."</a>".
                    " | submitted: ".date_format($comment_array_row[4], "m/d/Y h:ia").
                    $editLink."</i>\n");

                //If comment has been edited, display datetime of update
                if ($comment_array_row[6] == "1"){
                    echo "<b>Edited at ".date_format($comment_array_row[5], "m/d/Y h:ia")."</b></h2>";}
                else{
                    echo "</h2>";}

                //If logged in user is the author of the post (protects against URL editing)
                //and  edit link has been clicked, display text box to edit comment
                if ($loggedInUser == trim($thread_array[4]) && (isset($_GET['editClicked']) || $errorStatus == true) && $edited_post_id == $comment_array_row[0]){
                    
                    //If comment did not pass validation, keep it in text box
                    if (isset($_POST['edit_text'])){
                        $edit_text = $_POST['edit_text'];}

                    echo nl2br(
                        '<form action="?thread_id='.$thread_id.'&editSubmitted&edited_post_id='.$comment_array_row[0].'" method="post">'.
                        '<textarea name="edit_text" rows="4" cols="50" >'.
                        htmlspecialchars_decode(trim($edit_text)).'</textarea><br>'.
                        '<div class="error" id="edit_comment_error">'.$edit_comment_error.'</div><br>'.
                        '<input type="submit" value="Submit" name="submit_edit">'.
                        '</form>');}
                else{
                    echo nl2br(trim($comment_array_row[2])."\n\n");}   
            }
        ?>
        <br><br>
        <form action="?thread_id=<?php echo $thread_id ?>&<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
            <textarea name="comment_text" rows="4" cols="50" placeholder="Add comment"><?php echo $comment_text ?></textarea><br>
            <div class="error" id="comment_error"><?php echo ($comment_error) ?></div><br>
            <input type="submit" value="Submit" name="submit"><br>        
        </form>
    </div>

    </center>
</body>
</html>
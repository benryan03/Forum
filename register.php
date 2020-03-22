<?php
$username = "";
$password = "";
$confirmPassword = "";
$usernameError = "";
$passwordError = "";
$confirmPasswordError = "";
$submitError = "";
$errorStatus = false;
date_default_timezone_set("America/New_York");
$timestamp = date("Y/m/d h:i:sa");

if (!empty($_POST["submit"])){                  //if submit button clicked, then validate inputs
    $username = trim($_POST["username"]);       //trim() removes whitespace from  both sides of input
    $username = htmlspecialchars($username);    //convert html characters to html entities

    if (empty($username)){  //Check if username field is empty
        $usernameError = "Please enter a username.";
        $errorStatus = true;
    }
    elseif (!empty($username)) {
        //Check if username already exists

        //Connect to database
        $serverName = "localhost\sqlexpress";
        $connectionInfo = array("Database"=>"Forum", "UID"=>"ben", "PWD"=>"password123");
        $conn = sqlsrv_connect($serverName, $connectionInfo);

        //Query database for $username
        $isUsernameTakenQuery = "SELECT * FROM Users WHERE username = '$username' ";
        $isUsernameTaken = sqlsrv_query($conn, $isUsernameTakenQuery);
        
        //Convert result to boolean
        $isUsernameTaken = sqlsrv_fetch($isUsernameTaken);

        if ($isUsernameTaken == true){
            $usernameError = "Username taken";
            $errorStatus = true;
        }
    }

    $password = $_POST["password"];
    if (empty($password)){ //Check if password field is empty
        $passwordError = "Please enter a password.";
        $errorStatus = true;
    }
    $confirmPassword = $_POST["confirmPassword"];
    if (empty($confirmPassword)){ //Check if confirm password field is empty
        $confirmPasswordError = "Please confirm password.";
        $errorStatus = true;
    }
    if ($confirmPassword != $password){ //Check if passwords match
        $confirmPasswordError = "Passwords must match.";
        $errorStatus = true;
    }
    if ($errorStatus == false){
        //Convert password to hash
        $password = password_hash($password, PASSWORD_DEFAULT);

        //To calculate new user ID, count number of rows in database and add 1
        $countExistingUsersQuery = "SELECT * FROM Users";
        $countExistingUsers = sqlsrv_query($conn, $countExistingUsersQuery, array(), array( "Scrollable" => 'static' ));
        $row_count = sqlsrv_num_rows( $countExistingUsers );
        $newUserID = $row_count + 1;

        //Write new account to database
        $userRegisterQuery = "INSERT INTO Users VALUES ('$newUserID', '$username', '$password', '$timestamp')";
        $writeToDatabase = sqlsrv_query($conn, $userRegisterQuery);

        //Error messages 
        if (!$conn){
            $errorStatus = true;
            $submitError = "Error connecting to database.";
        }
        if (!$writeToDatabase){
            $errorStatus = true;
            $submitError = "Error writing to database.";
            //print_r(sqlsrv_errors());
        }
        if ($errorStatus == false){
            //Open success page
            header("Location:register_success.html");
        }


    }
    //print_r(sqlsrv_errors());
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

<div class="content">
<form class="register-form action="? echo $_SERVER["PHP_SELF"]" method="post">
<input type="text" name="username" placeholder="Username" value="<?php echo htmlentities($username) ?>"><br>
<span class="error"><?php echo "$usernameError" ?></span><br><br>

<input type="password" name="password" placeholder="Password" value="<?php echo htmlentities($password) ?>"><br>
<span class="error"><?php echo "$passwordError" ?></span><br><br>

<input type="password" name="confirmPassword" placeholder="Confirm Password" value="<?php echo htmlentities($confirmPassword) ?>"><br>
<span class="error"><?php echo "$confirmPasswordError" ?></span><br><br>

<input type="submit" value="Submit" name="submit"><br>
<span class="error"><?php echo "$submitError" ?></span>
</form>
</div>

</center>
</body>
</html>
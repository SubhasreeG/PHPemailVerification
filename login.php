<?php

include('config.php');

if(isset($_SESSION['id']))
{
 header("location:homepage.php");
}

$message = '';

$email = $password = "";
$email_err = $password_err = "";

if(isset($_POST["login"])){
	// Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";
    } else{
        $email = trim($_POST["email"]);
    }
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password, email_status FROM user_details WHERE email = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            $param_email = $email;
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){                    

                    mysqli_stmt_bind_result($stmt, $id, $username, $stored_password, $email_status);
                    if(mysqli_stmt_fetch($stmt)){
                        if($password == $stored_password){
                            if($email_status == 'verified'){
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username; 
                                header("location:homepage.php");
                            }
                            else{
                                $password_err = "Email id not verified, please verify before logging in";
                            }
                        } else{
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    $email_err = "No account found with that email.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body style=" background-image: url('background_img.jpg');">
    <br />
    <div class="container" style="width:100%; max-width:600px">
        <br />
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Login</h4>
            </div>
            <div class="panel-body">
                <form method="post">
                    <?php echo $message; ?>
                    <div class="form-group">
                        <label>User Email</label>
                        <input type="email" name="email" class="form-control" required />
                        <span class="help-block">
                            <?php echo $email_err; ?>
                        </span>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required />
                        <span class="help-block">
                            <?php echo $password_err; ?>
                        </span>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="login" value="Login" class="btn btn-info" />
                    </div>
                </form>
                <p>Don't have an account? <a href="index.php"> Register</a></p>
            </div>
        </div>
    </div>
</body>

</html>
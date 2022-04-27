<?php

// Include config file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once "config.php";

if(isset($_SESSION['user_id']))
{
 header("location:login.php");
}
 
$username = $password = $confirm_password = $name = $email= "";
$username_err = $password_err = $email_err = "";
$message = '';
if($_SERVER["REQUEST_METHOD"] == "POST"){
	// Validate email
	if(empty(trim($_POST["email"]))){
        $email_err = "Please enter emailid.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM user_details WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This mail id is already taken.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM user_details WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z])$/';
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } elseif(!preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])[0-9A-Za-z!@#$%]{6,12}$/',$_POST["password"])){
		$password_err = "Password must have atleat one digit,capital and small letter";
    }	
	else{
        $password = trim($_POST["password"]);
    }
    if(empty($username_err) && empty($password_err) ){
        $activation_code = md5(rand());
        $sql = "INSERT INTO user_details (email,username,password,activation_code,email_status) VALUES (?, ?, ?, ?, ?)";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "sssss",$param_email,$param_username, $param_password, $param_activation_code, $param_email_status);
            $param_username = $username;
			$param_email = $email;
            $param_password = $password; 
            $param_activation_code = $activation_code; 
            $param_email_status = 'not verified';
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $base_url = "http://localhost/emailverification/";
                $mail_body = "
                <p>Hey ".$_POST['username']."!</p>
                <p>Glad to have you on board! You can access the portal after your email verification.</p>
                <p>Head on to - ".$base_url."email_verification.php?activation_code=".$activation_code." to verify your email address
                <p>Best Regards,<br />Registration team</p>
                ";

                //Sending verification mail
                $mail = new PHPMailer(True);
                $mail->IsSMTP();        
                $mail->SMTPAuth = true;       
                $mail->SMTPSecure = 'tls';

                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 587;
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
                $mail->Username = 'gsubhasree2@gmail.com';     
                $mail->Password = 'Gss@3602';     
                $mail->SMTPSecure = '';       
                $mail->From = 'verify-email@registration.in';   
                $mail->FromName = 'email-verification';     
                $mail->AddAddress($_POST['email'], $_POST['username']);  
                $mail->WordWrap = 50;      
                $mail->IsHTML(true);          
                $mail->Subject = 'Email Verification';   
                $mail->Body = $mail_body;       
                if($mail->Send())        
                {
                    $message = '<label class="text-success">Registered! check your mail.</label>';
                }
            } else{
                echo "Something went wrong. Please try again later.";
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
    <title>Register</title>
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
                <h4>Register</h4>
            </div>
            <div class="panel-body">
                <form method="post" id="register_form">
                    <?php echo $message; ?>
                    <div class="form-group">
                        <label>User Name</label>
                        <input type="text" name="username" class="form-control" pattern="[a-zA-Z ]+" required />
                        <span class="help-block"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>User Email</label>
                        <input type="email" name="email" class="form-control" required />
                        <span class="help-block"><?php echo $email_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required />
                        <span class="help-block"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="register" id="register" value="Register" class="btn btn-info" />
                    </div>
                </form>
                <p>Already have verified account? <a href="login.php"> Login</a></p>
            </div>
        </div>
    </div>
</body>

</html>
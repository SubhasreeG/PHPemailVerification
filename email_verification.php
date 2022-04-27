<?php

include('config.php');

$message = '';

if(isset($_GET['activation_code']))
{
 $query = "SELECT id, email_status FROM user_details WHERE activation_code = ?";
 if($stmt = mysqli_prepare($link, $query)){
    mysqli_stmt_bind_param($stmt, "s", $param_email);
    $param_email = trim($_GET['activation_code']);
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_store_result($stmt);
        
        if(mysqli_stmt_num_rows($stmt) >= 1){
            mysqli_stmt_bind_result($stmt, $id, $status);

            while (mysqli_stmt_fetch($stmt)) {
        
                if($status == 'not verified')
                {
                    $update_query = "UPDATE user_details SET email_status = 'verified' WHERE id = '$id'";
			        mysqli_query($link,$update_query);
                    
                    $message = '<label class="text-success">Your Email Address Successfully Verified <br />You can login here - <a href="login.php">Login</a></label>';
                }
                else
                {
                    $message = '<label class="text-info">Your Email Address Already Verified</label>';
                }
            }
        }
        else
        {
            $message = '<label class="text-danger">Invalid Link</label>';
        }
    }
}
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Email Verification</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body style=" background-image: url('background_img.jpg');">

    <div class="container">
        <div class="panel panel-default">
            <h1 align="center">Email Verification</h1>

            <h3>
                <?php echo $message; ?>
            </h3>
        </div>
    </div>

</body>

</html>


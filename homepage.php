<?php

include('config.php');

?>

<!DOCTYPE html>
<html>

<head>
    <title>Welcome</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body style=" background-image: url('background_img.jpg');">
    <br />
    <div class="container" style=" text-align: center; width:100%; max-width:600px">
        <div class="panel panel-default">
            <h3>Welcome  <b><?php echo htmlspecialchars($_SESSION["username"]); ?>! </h3>
            <h4><a href="logout.php" class="nav-link">LogOut</a></h4><br /><br />
        </div>
    </div>
</body>

</html>
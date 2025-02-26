<?php
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$heard = $_POST['heard'];
$comments = $_POST['comments'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Signing Up</title>
</head>
<body>
    <h1>Thank You for Signing Up!</h1>
    <h2>You have entered the following information</h2>
    <p>Name: <?php echo $name; ?></p>
    <p>Phone: <?php echo $phone; ?></p>
    <p>Email: <?php echo $email; ?></p>
    <p>How you heard about us: <?php echo $heard; ?></p>
    <p>Comments: <?php echo $comments; ?></p>
</body>
</html>


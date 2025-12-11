<?php
if (isset($_POST['username']) && isset($_POST['password'])) {
    //write code check it
    header("index2.php");

}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form method="POST" action="">
    <h1 style="color: #ffb3b3">Log in</h1>
    <input type="text" name="username" placeholder="Username"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <input type="submit" value="Login">
    <a href="SingIn.php">i don't have an account</a>
</form>

</body>
</html>
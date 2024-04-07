<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once ("functions.php");
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    session_regenerate_id();
    if (login($username, $password)) {

        header("location:home.php");
    } else {
        echo 'Invalid username or password';
    }
} ?>
<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
</head>

<body>
    <h1>Login</h1>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <input type="submit" value="Login">
    </form>
    <a href="register.php">Register</a>

</body>

</html>
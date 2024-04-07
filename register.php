<?php
require_once ("functions.php");
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['nickname']) && isset($_POST['role'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nickname = $_POST['nickname'];
    $role = $_POST['role'];
    if (register($username, $password, $nickname, $role)) {
        header("location:login.php");
    } else {
        echo 'Invalid username or password';
    }
}

?>

<html>
<header>
    <title>Register</title>
</header>


<body>
    <h1>Register</h1>
    <form name="registerForm" action="register.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>
        <label for="password">Retype Password:</label>
        <input type="password" name="retypepassword" id="retypepassword" required>
        <br>
        <label for="nickname">Nickname:</label>
        <input type="text" name="nickname" id="nickname" required>
        <br>
        <label for="role">Select role:</label>
        <select name="role" id="role" required>
            <option value="">Choose: </option>
            <option value="moderator">moderator</option>
            <option value="villager">villager</option>
        </select>
        <br>
        <input type="button" value="Register" onclick="javascript:validateForm()">
    </form>
    <?php
    ?>
    <a href="login.php">Login</a>
    <script>
        function validateForm() {
            var x = document.forms["registerForm"]["password"].value;
            var y = document.forms["registerForm"]["retypepassword"].value;
            if (x != y) {
                alert("Password not match");
                return false;
            }
            else (document.forms["registerForm"].submit());
        }
        document.getElementById("retypepassword").addEventListener("blur", function () {
            var x = document.forms["registerForm"]["password"].value;
            var y = document.forms["registerForm"]["retypepassword"].value;
            if (x != y)

                alert("Password not match");

        });
    </script>
</body>

</html>
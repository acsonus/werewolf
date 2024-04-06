<?php
// create a page with personal information and current role (werewolf, villager, seer, etc.)

session_start();
if (!isset($_SESSION['user'])) {
    header("location:login.php");
}

require ('connection.php');

$sql = 'SELECT * FROM user WHERE name = "' . $_SESSION['user'] . '"';

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $role = $row['role'];
    $username = $row['name'];
    $status = $row['status'];
} else {
    echo 'Error: ' . $sql . '<br>' . $conn->error;
}
?>


<html>

<head>
    <title>Home</title>
</head>

<body>
    <h1>Welcome
        <?php echo $username; ?>
    </h1>
    <p>Your role is:
        <?php echo $role; ?>
    </p>
    <p>Your status is:
        <?php echo $status; ?>
    </p>
    <a href="logout.php">Logout</a>

</body>

</html>
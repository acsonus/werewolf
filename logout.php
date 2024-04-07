<?php
?>
<html>

<head>
    <title>Logout</title>
</head>
<?php
require_once("functions.php");
if (isset($_GET['action']) == 'logout') {
    session_destroy();
    // delete all games i am participating in 
    clearAllMyParticipations($_SESSION['user_id']);
    header("location:login.php");
}
?>

<body>
    <h1>Logout</h1>
    <p>Are you sure you want to logout?</p>
    <form action="logout.php?action=logout" method="post">
        <input type="submit" value="Logout">
       
    </form>
    <button onclick="window.location.href='home.php'">Cancel</button>
</body>

</html>
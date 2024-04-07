<?php

require_once("functions.php");

// this part describes behaviour for player
if ($_SESSION['role']=="villager" || $_SESSION['role']=="werewolf" || $_SESSION['role']=="vampire") {
    if (isset($_POST['vote'])) {
        $vote = $_POST['vote'];
        $gameId = $_SESSION['gameId'];
        $voterId = $_SESSION['user_id'];
        for ($i = 0; $i < count($_SESSION['players']); $i++) {
            if ($_SESSION['players'][$i]['nickname'] == $vote) {
                $votedId = $_SESSION['players'][$i]['id'];
            }
        }
        if (vote($gameId, $voterId, $votedId)) {
            header("location:home.php");
        } else {
            echo "Invalid vote";
        }
    }
}



?>
<html>

<head>
    <title>Vote Page</title>
</head>

<body>

    <h1>Vote</h1>
    <form action="vote.php" method="post">
        <label for="vote">Vote:</label>
        <?php if ($_SESSION["daynight"]=="day") getAllAlivePlayers($_SESSION['gameId'])?>
        <?php if ($_SESSION["daynight"]=="night" && ($_SESSION["role"]=="werewolf" || $_SESSION["role"]="vampire")) getAllAlivePlayers($_SESSION['gameId'])?>
        <input type="text" name="vote" id="vote" required>
        <br>
        <input type="submit" value="Vote">
    </form>
    <a href="home.php">Back</a>
</body>
</html>
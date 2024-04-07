<?php
// create a page with personal information and current role (werewolf, villager, seer, etc.)

if (session_status() == PHP_SESSION_NONE) {
    session_start();

}
if (!isset($_SESSION['user'])) {
    header("location:login.php");
}

require_once ('connection.php');
$sql = "SELECT * FROM user WHERE name = '" . $_SESSION['user'] . "'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $role = $row['role'];
    $username = $row['name'];
    $status = $row['status'];
    $nickname = $row['nickname'];
} else {
    error_log('Error: ' . $sql . '<br>' . $conn->error);
}

//get current game status
if (isset($_SESSION['curent_game_id']) && $_SESSION['curent_game_id'] != null) {
    $result = $conn->query("SELECT * FROM game WHERE id = " . $_SESSION['curent_game_id']);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $gameStatus = $row['status'];
    } else {
        echo "<p>problem loading game status</p>";
    }
}

//store all session players
if (isset($_SESSION['curent_game_id']) && $_SESSION['curent_game_id'] != null) {
    $result = $conn->query("SELECT user.name, user.role, user.nickname 
                            from user 
                            inner join user_game on user.id = user_game.user_id 
                            where user_game.game_id = " . $_SESSION['curent_game_id'] . " and user.role!='dead' and user.role!='moderator'");
    if ($result->num_rows > 0) {
        $players = array();
        while ($row = $result->fetch_assoc()) {
            $players[] = $row;
        }
        $_SESSION['players'] = $players;
    } else {
        echo "<p>problem loading players</p>";
    }
    //get daynight
    $query = "SELECT daynight,round_number
                            from game_round 
                            inner join game 
                            where game_round.game_id = " . $_SESSION['curent_game_id'] . " order by game_round.id desc limit 1";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['daynight'] = $row['daynight'];
    } else {
        echo "problem loading day and daynight parameters<br>";
        //error_log('Error: ' . $sql . '<br>' . $conn->error);
    }
}
//start the game 
if (isset($_GET['mode']) && ($_GET['mode'] == 'start_game')) {
    $//conn->query("UPDATE game SET status = 'active' WHERE id = " . $_SESSION['curent_game_id']);
        $conn->query("INSERT INTO game_round (game_id,daynight,round_number) VALUES (" . $_SESSION['curent_game_id'] . ",'day',1)");
    header("location:home.php");
}


//check current game 
if (isset($_GET['mode']) && ($_GET['mode'] == 'leave')) {
    $_SESSION['curent_game_id'] = null;
    $conn->query("DELETE FROM user_game WHERE user_id = " . $_SESSION['user_id']);
    header("location:home.php");
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
    <?php if ((!isset($_SESSION['curent_game_id'])) || ($_SESSION['curent_game_id'] == null)) { ?>
        <p>You are not in the game</p>
    <?php } else {

        $result = $conn->query("SELECT game.name as name,game.id as id from game inner join user_game on game.id = user_game.game_id where user_game.user_id = " . $_SESSION['user_id']);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $gameName = $row['name'];
            $_SESSION["curent_game_id"] = $row['id'];
            echo 'You are in the game: ' . $gameName;
        } else {
            echo '<p>Game is not started by moderator</p>';
        }

        if (isset($_SESSION['daynight']) && $_SESSION['daynight'] == 'night') {
            echo '<p>Now is Night</p>';
        }
        if (isset($_SESSION['daynight']) && $_SESSION['daynight'] == 'day') {
            echo '<p>Now is Day</p>';
        }

    }

    ?>



    <p>Your role is:
        <?php echo $role; ?>
    </p>
    <p>Your nickname is:
        <?php echo $nickname; ?>
    </p>
    <p>Your status is:
        <?php echo $status; ?>
    </p>

    <?php if (isset($_SESSION['curent_game_id'])) { ?>
        <?php if (isset($_SESSION['players'])) { ?>
            <p>Players:</p>
            <ul>
                <?php foreach ($_SESSION['players'] as $player) { ?>
                    <li>
                        <?php echo $player['nickname'] . ' (' . $player['role'] . ')'; ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p> No players in the game</p>
        <?php } ?>
    <?php } else { ?>
        <a href="game.php?mode=join">Join Game</a>
    <?php } ?>
    <?php if ($_SESSION['role'] == 'moderator' && (!isset($_SESSION['curent_game_id']))) { ?>
        <a href="game.php?mode=create">Create Game</a>
    <?php } ?>
    <?php if ($_SESSION['role'] == 'moderator' && (!isset($_SESSION['curent_game_id']))) { ?>
        <a href="game.php?mode=start_game">Start Game</a>
    <?php } ?>
    <a href="logout.php">Logout</a>

    <?php if (isset($_SESSION["curent_game_id"])) { ?>
        <a href="game.php?mode=leave">Leave Game</a>
        <?php if (
            (isset($_SESSION['daynight']) && $_SESSION['daynight'] == 'day' && $_SESSION['role'] == "villager") ||
            (isset($_SESSION['daynight']) && $_SESSION['daynight'] == 'night' && ($_SESSION['role'] == "vampire" || $_SESSION['role'] = "werewolf"))
        ) { ?>
            <a href="vote.php">Vote</a>
        <?php }
    } ?>
</body>

</html>
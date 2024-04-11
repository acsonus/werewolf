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
        $_SESSION['game_status'] = $gameStatus;
    } else {
        echo "<p>problem loading game status</p>";
    }
}

// update $_SESSION['game_status'] session variable
if (isset($_SESSION['curent_game_id']) && $_SESSION['curent_game_id'] != null) {
    $result = $conn->query("SELECT status FROM game WHERE id = " . $_SESSION['curent_game_id']);

    if ($row = $result->fetch_assoc()) {
        $_SESSION['game_status'] = $row['status'];
    }
}

//store all session players
if (isset($_SESSION['curent_game_id']) && $_SESSION['curent_game_id'] != null && $_SESSION['game_status'] == 'started') {
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

    // get current game round
    $conn->query("select max(game_round_id) from  game_round  where  game_id=" . $_SESSION['curent_game_id']);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['game_round_id'] = $row['game_round_id'];
    } else {
        echo "problem loading game round<br>";
        //error_log('Error: ' . $sql . '<br>' . $conn->error);
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
    //conn->query("UPDATE game SET status = 'active' WHERE id = " . $_SESSION['curent_game_id']);
    $conn->query("UPDATE game SET status = 'started' WHERE id = " . $_SESSION['curent_game_id']);
    $conn->query("INSERT INTO game_round (game_id,daynight,round_number) VALUES (" . $_SESSION['curent_game_id'] . ",'night',1)");
    header("location:home.php");
}
// set main vampire or werewolf
if (isset($_GET['user_id']) && isset($_GET['set_main']) && ($_GET['set_main'] == 'vampire' || $_GET['set_main'] == 'werewolf')) {
    $mainRole = $_GET['set_main'];
    $conn->query("UPDATE user SET role = '" . $role . "' WHERE id = " . $_GET['user_id']);
    header("location:home.php");
}
// change daynight by moderator 
if ($_SESSION["role"]=="moderator" && isset($_GET['mode']) && ($_GET['mode'] == 'change_daynight')) {
    switchDayNight($_SESSION['curent_game_id'],$_SESSION['current_game_round']);
    header("location:home.php");
}
//change round by moderator
if ($_SESSION["role"]=="moderator" && isset($_GET['mode']) && ($_GET['mode'] == 'change_round')) {
    $round = getMaxGameRound($_SESSION["curent_game_id"])+1;
    $conn->query("INSERT INTO game_round (game_id,daynight,round_number) VALUES (" . $_SESSION['curent_game_id'] . ",'night',$round)");
    header("location:home.php");
}




//leave current game
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
                        <?php echo $player['nickname'];
                        if ($_SESSION['role'] == 'moderator')
                            echo ' ( ' . $player['role'] . ')';
                        if ($_SESSION['daynight'] == 'night' && ($player['role'] == 'vampire' || $player['role'] == 'werewolf') && isset($_SESSION["current_game_round"]))
                            echo '<a href=bite.php?bite_id=' . $player['id'] . '&current_game_round=' . $_SESSION["current_game_round"] . '>Bite</a>';
                        if ($_SESSION['daynight'] == 'day' && ($player['role'] != 'moderator') && isset($_SESSION["current_game_round"]))
                            echo '<a href=vote.php?vote_id=' . $player['id'] . '&current_game_round=' . $_SESSION["current_game_round"] . '>Vote</a>';
                        if ($_SESSION['daynight'] == 'night' && ($player['role'] == 'vampire' || $player['role'] == 'werewolf') && isset($_SESSION["current_game_round"])) {
                            echo '<a href=protect.php?protect_id=' . $player['id'] . '&current_game_round=' . $_SESSION["current_game_round"] . '>Protect</a>';
                        }
                        if ($_SESSION['role'] == 'moderator' && $player['role'] == 'villager' && isset($_SESSION["current_game_round"])) {
                            echo '<a href=home.php?set_main=vampire&user_id=' . $player['id'] . '>Set main</a>';
                        }
                        ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p> No players in the game</p>
        <?php } ?>
 <!-- this section is for moderator. here moderator can change day and night and also can change round number-->
        <?php if ($_SESSION['role'] == 'moderator') { ?>
           <a href="home.php?mode=change_daynight">Switch to <?php if ($_SESSION['daynight'] == "night") echo "day"; else echo "night";?></a>
            <a href="home.php?mode=change_round">Change Round to <?php echo getMaxGameRound($_SESSION["curent_game_id"])+1; ?></a>
        <?php } ?>
    <?php } else { ?>
        <a href="game.php?mode=join">Join Game</a>
    <?php } ?>
    <?php if ($_SESSION['role'] == 'moderator' && (!isset($_SESSION['curent_game_id']))) { ?>
        <a href="game.php?mode=create">Create Game</a>
    <?php } ?>
    <?php if ($_SESSION['role'] == 'moderator' && isset($_SESSION['curent_game_id']) && $_SESSION['game_status'] == 'created') { ?>
        <a href="home.php?mode=start_game">Start Game</a>
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
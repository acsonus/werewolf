<?php
require_once ("functions.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//this part describes how moderator creates the game
if ($_SESSION['role'] == "moderator" && isset($_POST['gameName']) && isset($_POST['gamePassword'])) {
    if (isset($_POST['gameName']) && isset($_POST['gamePassword'])) {
        $gameName = $_POST['gameName'];
        $gamePassword = $_POST['gamePassword'];
        $user_id = $_SESSION["user_id"];
        if (isset($_POST['gameDescription'])){
            $description = $_POST['gameDescription'];
        }
        else {
            $description = "";
        }
        createGame($gameName, $gamePassword, $user_id,$description);
        header("location:home.php");
    }
}

// this part describes how player joins the game
if (isset($_POST['avaliableGames']) && isset($_POST['gamePassword']) && isset($_POST['mode']) && $_POST['mode'] == "join") {
    $game_id = $_POST['avaliableGames'];
    $gamePassword = $_POST['gamePassword'];
    $user_id = $_SESSION["user_id"];
    $role = $_SESSION["role"];
    if (checkGamePassword($game_id, $gamePassword)) {
        if (joinGame($user_id, $game_id, ($role==="moderator")?1:0)) {
            $_SESSION["curent_game_id"] = $game_id;
            header("location:home.php");
        } else {
            echo "You are already in this game";
        }
    } else {
        echo "Invalid Game password";
    }

}
// this part describes how player leaves the game
if (isset($_POST['decision']) && $_POST['decision'] == "leave") {
    $user_id = $_SESSION["user_id"];
    $game_id = $_SESSION["curent_game_id"];
    leaveGame($game_id, $user_id);
    $_SESSION["curent_game_id"] = null;
    header("location:home.php");
}
?>

<html>

<head>
    <title>Game</title>

</head>

<body>
    <!-- this part describes form for moderator-->
    <?php

    if ($_SESSION['role'] == "moderator" && isset($_GET['mode']) && $_GET['mode'] == "create") {
        ?>
        <form action="game.php" method="post">
            <div>
                <p>Game name </p>
                <input type="text" name="gameName" id="gameName" required>
            </div>
            <div>
                <p>Game password </p>
                <input type="password" name="gamePassword" id="gamePassword" required>
            </div>
            <div>
                <p>Description:</p>
                <textarea name="gameDescription" id="gameDescription"></textarea> 
            </div>
            <input type="submit" value="Create Game">
        </form>
    <?php }
    // this part describes the game from the players site 
    if (isset($_GET['mode']) && $_GET['mode'] == "join") {

        ?>
        <!--- this part describes the game from the players site -->
        <form action="game.php?mode=join" method="post">
            <div>
                <select id="avaliableGames" name="avaliableGames">
                    <option value="">Choose game</option>
                    <?php
                    loadAvaliableGames();
                    ?>
                </select>
            </div>
            <div>
                <label for="gamePassword">Game Password:</label>
                <input type="password" name="gamePassword" id="gamePassword" required>
                <input type="hidden" name ="mode" value="join">
            </div>
            <div>
                <input type="submit" value="Join Game">
            </div>
        </form>

    <?php }
    // this part describes a situation when player wants to leave the game
    if (isset($_GET['mode']) && $_GET['mode'] == "leave") {
        ?>
        <p>You want to leave the game?</p>
        <form action="game.php" method="post">
            <label for="gamePassword">Are you sure?</label>
            <input id="decision" type="hidden" name="decision" value="leave">
            <input type="submit" name="submitLeave" id="submitLeave" value="Yes">
        </form>
    <?php }
    ?>




    <a href="home.php">Back</a>
</body>

</html>
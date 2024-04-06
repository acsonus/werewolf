<?php
require_once ("functions.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//this part describes behaviour for moderator

// this part describes behaviour for player
?>

<html>

<head>
    <title>Game</title>

</head>

<body>
    <!-- this part describes the game from moderator site-->
    <?php

    if ($_SESSION['role'] == "moderator") {
        ?>
        <form action="game.php" method="post">
            <div>
                <p>Game name </p>
                <input type="text" name="gameName" id="gameName" required>
            </div>
            <div>
                <p>Game name </p>
                <input type="password" name="gamePassowrd" id="gameName" required>
            </div>
            <input type="submit" value="Create Game">
        </form>
    <?php } else { ?>
        <!--- this part describes the game from the players site -->
        <form action="game.php">
            <div>
                <select id="avaliableGames" name="avaliableGames">
                    <?php
                    loadAvaliableGames();
                    ?>
                </select>
            </div>

        </form>
    <?php } ?>

</body>

</html>
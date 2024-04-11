<?php

require_once("functions.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// this part describes behaviour for player
if ($_SESSION['role']=="villager" || $_SESSION['role']=="werewolf" || $_SESSION['role']=="vampire") {
    if (isset($_REQUEST['vote_id'])) {
        $vote = $_REQUEST['vote_id'];
        $gameId = $_SESSION['gameId'];
        $voterId = $_SESSION['user_id'];
        if (vote($gameId, $voterId, $votedId)) {
            header("location:home.php");
        } else {
            echo "Invalid vote";
        }
    }
}
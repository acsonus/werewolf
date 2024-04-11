<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_REQUEST['protected_id']) && isset($_REQUEST["current_game_round"])){
    $protected_id = $_REQUEST['protected_id'];
    $current_game_round = $_REQUEST["current_game_round"];
    
    if ($_SESSION["role"]=='werewolf'){
        $conn->query("insert into game_round_protected (user_id,game_round_id,protected_by_werevolf,protected_by_vampire) values ($protected_id,$current_game_round, b'1',b'0')");
    }
    if ($_SESSION["role"]=='vaampire'){
        $conn->query("insert into game_round_protected (user_id,game_round_id, protected_by_werevolf,protected_by_vampire) values ($protected_id,$current_game_round,b'0',b'1')");
    }
}

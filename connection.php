<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$params = parse_ini_file(".env");
//$conn = new mysqli("sql108.infinityfree.com", "if0_36310974", "nv7sAI9bmdmB", "if0_36310974_werewolf");
$conn = new mysqli($params["url"], $params["user"], $params["pwd"], $params["db"]);

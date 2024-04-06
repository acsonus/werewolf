<?php


if (session_status() == PHP_SESSION_NONE) {
    session_start();
    header("location:home.php");
}

?>
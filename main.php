<?php
// login form php with session

session_start();
if(isset($_SESSION['user'])){
    header("location:home.php");
}

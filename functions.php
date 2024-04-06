<?php
require_once("connection.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function login($username, $password){
    global $conn;
    $sql = "SELECT * FROM user WHERE name = '$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $dbPassword = $row["password"];
        if (!password_verify($password, $dbPassword)) {
            return false;
        }else {
            $_SESSION['user'] = $row['name'];
            return true;
        }
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}

function register($username, $password, $nickname)
{
    global $conn;
    $passwordHash  = password_hash($password, PASSWORD_ARGON2I);
    $sql = "INSERT INTO user (name, password,nickname) VALUES ('$username', '$passwordHash', '$nickname')";
    if ($conn->query($sql) === true) {
        return true;
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}

function loadAvaliableGames(){
    global $conn;
    $stmt = $conn->query("SELECT * FROM game"); // Replace with your table and column name
    // set the resulting array to associative
    while ($row = $stmt->fetch_assoc()){
        echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
    }

}
function createGame($name,$password,$moderator){
    global $conn;
    $passwordHash  = password_hash($password, PASSWORD_ARGON2I);
    $sql = "INSERT INTO game (name, password,moderator) VALUES ('$name', '$passwordHash', 1)";
    if ($conn->query($sql) === true) {
        return true;
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}
function joinGame($userId, $gameId){

    global $conn;
    $sql = "INSERT INTO user_game (user_id, game_id) VALUES ('$userId', '$gameId')";
    if ($conn->query($sql) === true) {
        return true;
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}
?>
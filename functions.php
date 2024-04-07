<?php
require_once ("connection.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
function login($username, $password)
{
    global $conn;
    $sql = "SELECT * FROM user WHERE name = '$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $dbPassword = $row["password"];
        if (!password_verify($password, $dbPassword)) {
            return false;
        } else {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['nickname'] = $row['nickname'];
            return true;
        }
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}

function register($username, $password, $nickname, $role)
{
    global $conn;
    $passwordHash = password_hash($password, PASSWORD_ARGON2I);
    $sql = "INSERT INTO user (name, password,nickname,role,status) VALUES ('$username', '$passwordHash', '$nickname', '$role', 'active')";
    if ($conn->query($sql) === true) {
        return true;
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}

function loadAvaliableGames()
{
    global $conn;
    $stmt = $conn->query("SELECT * FROM game"); // Replace with your table and column name
    // set the resulting array to associative
    while ($row = $stmt->fetch_assoc()) {
        echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
    }

}
function createGame($name, $password, $moderatorid)
{
    global $conn;
    $passwordHash = password_hash($password, PASSWORD_ARGON2I);

    $st = $conn->query("select ifnull(max(id)+1,1) as id from game");
    if ($row = $st->fetch_assoc()) {
        $id = $row["id"];
        $sql = "INSERT INTO game (id,name, password) VALUES ($id,'$name', '$passwordHash')";
        if ($conn->query($sql) === false) {
            error_log('Error: ' . $conn->error);
        }
        $sql = "INSERT INTO user_game (user_id, game_id,) VALUES ($moderatorid, $id)";
        if ($conn->query($sql) === false) {
            error_log('Error: ' . $conn->error);
        }
        return true;
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}
function joinGame($userId, $gameId, $role)
{

    global $conn;


    $sql = "select user_id, game_id from  user_game where user_id = $userId and game_id = $gameId";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row["user_id"] == $userId && $row["game_id"] == $gameId) {
                return false;
            }
        }
    }

    $sql = "INSERT INTO user_game (user_id, game_id,moderator) VALUES ('$userId', '$gameId', b'$role')";
    if ($conn->query($sql) === true) {
        return true;
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}
function checkGamePassword($gameId, $password)
{
    global $conn;
    $sql = "SELECT * FROM game WHERE id = '$gameId'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $dbPassword = $row["password"];
        if (!password_verify($password, $dbPassword)) {
            return false;
        } else {
            return true;
        }
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}

function getAllAlivePlayers($gameId)
{
    global $conn;
    $sql = "SELECT user.name as name ,user_id as id, user.nickname as nickname 
            FROM user_game JOIN user ON user_game.user_id = user.id 
            WHERE game_id = $gameId and user.role!='dead";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<fieldset><legend>Who is the beast?:</legend>";
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<input type='radio' id = 'vote_'" . $row["user_id"] . "name='vote_'" . $row["user_id"] . " value='" . $row["user_id"] . "'/>";
            echo "<label for='vote_" . $row["user_id"] . "'>" . $row["name"] . " aka " . $row["nickname"] . "</label>";
            echo "</div>";
        }
        echo "</fieldset>";

    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}

function getAllVictumPlayers($gameId)
{
    global $conn;
    $sql = "SELECT user.name as name ,user_id as id, user.nickname as nickname 
            FROM user_game JOIN user ON user_game.user_id = user.id 
            WHERE game_id = $gameId and user.role!='dead' and user.role!='moderator'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        //section bite for werewolf and vampire
        echo "<fieldset><legend>Who will be bitten this night?:</legend>";
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<input type='radio' id = 'bite_'" . $row["user_id"] . "name='bite_'" . $row["user_id"] . " value='" . $row["user_id"] . "'/>";
            echo "<label for='bite_" . $row["user_id"] . "'>" . $row["name"] . " aka " . $row["nickname"] . "</label>";
            echo "</div>";
        }
        echo "</fieldset>";
        //section protect for werewolf and vampire
        echo "<fieldset><legend>Who will be protected this night?:</legend>";
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<input type='radio' id = 'protect_'" . $row["user_id"] . "name='protect_'" . $row["user_id"] . " value='" . $row["user_id"] . "'/>";
            echo "<label for='eat_" . $row["user_id"] . "'>" . $row["name"] . " aka " . $row["nickname"] . "</label>";
            echo "</div>";
        }
        echo "</fieldset>";


    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}

function vote($gameroundId, $user_id, $vote_user_id)
{
    global $conn;
    $sql = "INSERT INTO game_round_vote (game_round_id, user_id, vote_user_id) VALUES ($gameroundId, $user_id, $vote_user_id)";
    if ($conn->query($sql) === true) {
        return true;
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}

function newRound($gameId)
{
    global $conn;
    $sql = "select ifnull(max(id)+1,1) as id from game_round";
    if ($row = $conn->query($sql)->fetch_assoc()) {
        $id = $row["id"];
        $sql = "INSERT INTO game_round (id, game_id, round_number,daynight) VALUES ($id, $gameId, ifnull(select max(round_number)+1 from game_round where game_id = $gameId,1),'night')";
        if ($conn->query($sql) === true) {
            return $id;
        } else {
            error_log('Error: ' . $conn->error);
            return false;
        }
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}

function getLastRoundNumber($gameId)
{
    global $conn;
    $sql = "select ifnull(max(round_number),0) as round_number from game_round where game_id = $gameId";
    if ($row = $conn->query($sql)->fetch_assoc()) {
        return $row["round_number"];
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}

function setRoundDayNight($gameId, $roundId, $daynight)
{
    global $conn;
    $sql = "UPDATE game_round SET daynight = '$daynight' WHERE game_id = $gameId and id = $roundId";
    if ($conn->query($sql) === true) {
        return true;
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}

function clearAllMyParticipations($id)
{
    global $conn;
    $sql = "DELETE FROM user_game WHERE user_id = $id";
    if ($conn->query($sql) === true) {
        return true;
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}

function leaveGame($userId, $gameId)
{
    global $conn;
    $sql = "DELETE FROM user_game WHERE user_id = $userId and game_id = $gameId";
    if ($conn->query($sql) === true) {
        return true;
    } else {
        error_log('Error: ' . $conn->error);
        return false;
    }
}
?>
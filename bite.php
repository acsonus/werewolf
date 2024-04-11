<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_REQUEST['bite_id'])){
    $bite_id = $_REQUEST['bite_id'];
    $gameRoundId = $_REQUEST['game_round_id'];
    if ($_SESSION["role"]=='vampire'){
        $bite_id = $_REQUEST['bite_id'];
        $conn->query("insert into game_round_bitten (user_id,game_round_id,bitten_by_werevolf,bitten_by_vampire) values ($bite_id,$gameRoundId,b'1',b'0')");
    }
    if ($_SESSION["role"]=='werewolf'){
        $conn->query("insert into game_round_bitten (user_id,game_round_id,bitten_by_werevolf,bitten_by_vampire) values ($bite_id,$gameRoundId,b'0',b'1')");
        $conn->query($sql);
    }
}

?>
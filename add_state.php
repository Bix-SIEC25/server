<?php

if (!isset(
    $_REQUEST["x"],
    $_REQUEST["y"],
    $_REQUEST["dir"],
    $_REQUEST["wait_car"],
    $_REQUEST["qr"],
    $_REQUEST["face"],
    $_REQUEST["dialog"],
    $_REQUEST["fall_ia"],
    $_REQUEST["mov_car"],
    $_REQUEST["wait_image_verif"],
)) {
    header("Location: ./ko");
    exit;
}

$x = htmlspecialchars($_REQUEST["x"]);
$y = htmlspecialchars($_REQUEST["y"]);
$dir = htmlspecialchars($_REQUEST["dir"]);
$wait_car = htmlspecialchars($_REQUEST["wait_car"]);
$qr = htmlspecialchars($_REQUEST["qr"]);
$face = htmlspecialchars($_REQUEST["face"]);
$dialog = htmlspecialchars($_REQUEST["dialog"]);
$fall_ia = htmlspecialchars($_REQUEST["fall_ia"]);
$mov_car = htmlspecialchars($_REQUEST["mov_car"]);
$wait_image_verif = htmlspecialchars($_REQUEST["wait_image_verif"]);

// echo "Received: " .
//     "<br>x=" . $x .
//     "<br>y=" . $y .
//     "<br>dir=" . $dir .
//     "<br>wait_car=" . $wait_car .
//     "<br>qr=" . $qr .
//     "<br>face=" . $face .
//     "<br>dialog=" . $dialog .
//     "<br>fall_ia=" . $fall_ia .
//     "<br>mov_car=" . $mov_car .
//     "<br>wait_image_verif=" . $wait_image_verif;

// https://bix.ovh/add_state.php?x=4&y=2&dir=1.5&wait_car=1&qr=0&face=0&dialog=0&fall_ia=0&mov_car=0&wait_image_verif=0
// POST request supported too

include_once("../insa_db.php");
$db = dbConnect();
$updateQuery = "UPDATE `bix_state` 
    SET
        `x`=:x,
        `y`=:y,
        `dir`=:dir,
        `wait_car`=:wait_car,
        `qr`=:qr,
        `face`=:face,
        `dialog`=:dialog,
        `fall_ia`=:fall_ia,
        `mov_car`=:mov_car,
        `wait_image_verif`=:wait_image_verif
    WHERE 1";
        // `scenario`='[value-1]',
        // `last_step`=:last_step
$updateStmt = $db->prepare($updateQuery);
$updateStmt->execute([
    "x"=>$x,
    "y"=>$y,
    "dir"=>$dir,
    "wait_car"=>$wait_car,
    "qr"=>$qr,
    "face"=>$face,
    "dialog"=>$dialog,
    "fall_ia"=>$fall_ia,
    "mov_car"=>$mov_car,
    "wait_image_verif"=>$wait_image_verif,
]);

@file_get_contents(
    "http://127.0.0.1:6442/push",
    false,
    stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/plain\r\n",
        'content' => "bix/admin:state"
    ]])
);

header("Location: ./ok");
exit;

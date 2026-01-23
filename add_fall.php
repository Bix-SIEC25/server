<?php

if (!isset($_REQUEST["devid"]) || !isset($_REQUEST["mag"]) || !isset($_REQUEST["zone"])) {
    header("Location: ./ko");
    exit;
}

include_once("../insa_db.php");
$db = dbConnect();
include_once("scenario_mark.php");

$sqlQuery = 'INSERT INTO fall_alerts(device_id, jerkmagnitude, zone) VALUES (:dev, :mag, :zone)';

$insert = $db->prepare($sqlQuery);
$insert->execute([
    'dev' => htmlspecialchars($_REQUEST["devid"]),
    'mag' => htmlspecialchars($_REQUEST["mag"]),
    'zone' => htmlspecialchars($_REQUEST["zone"])
]);

$positions = [
    // "13.3|3.04|0.696",
    // "20.56|8.54|0.696", // (proche porte sortie)
    // "14.68|4.19|0.696",
    // "8.58|-0.3|0.696", // zone 3
    // "4.13|-3.58|0.696" // zone 4 (proche hall)

    // gei13 to gei 15                 gei15 to gei13
    [[9.25904, 0.09446, 0.621987], [11.4316, 1.83376, -2.49495]], // zone 1 hall
    [[12.1264, 2.27981, 0.648689], [14.4867, 3.95013, -2.58327]], // zone 2
    [[15.2141, 4.50699, 0.625084], [17.4361, 6.17878, -2.45382]], // zone 3
    [[18.1268, 6.77389, 0.610709], [20.3543, 8.33952, -2.47191]]  // zone 4 sortie
];

$stmt = $db->prepare("SELECT x, y, dir FROM bix_state");
$stmt->execute([]);
$res = $stmt->fetchAll();
$dir = 0.696;
$sens = 0;
if (sizeof($res) == 1) {
    $cx = $res[0]["x"];
    $cy = $res[0]["y"];
    $cdir = $res[0]["dir"];
    $tx = $positions[(int)$_REQUEST["zone"] - 1][0][0]; // defaulting on gei13 to gei15 direction
    $ty = $positions[(int)$_REQUEST["zone"] - 1][0][1];

    $dx = $tx - $cx;
    $dy = $ty - $cy;

    $destdir = atan2($dy, $dx);
    $sens = cos($destdir - 0.6) < 0; // 0.6 being the apprimative orientation of the corridor
    $dir = $positions[(int)$_REQUEST["zone"] - 1][$sens][2];
}

// echo "goto" . $positions[(int)$_REQUEST["zone"] - 1][0] . "|" . $positions[(int)$_REQUEST["zone"] - 1][1] . "|" . $dir;

@file_get_contents(
    "http://127.0.0.1:6442/push",
    false,
    stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/plain\r\n",
        'content' => "bix/wristband:new fall"
    ]])
);

@file_get_contents(
    "http://127.0.0.1:6442/push",
    false,
    stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/plain\r\n",
        'content' => "bix/fall_alert:fall>" . htmlspecialchars($_REQUEST["devid"]) . "|" . htmlspecialchars($_REQUEST["zone"])
    ]])
);

// header("Location: ./ok");
// exit;


@file_get_contents(
    "http://127.0.0.1:6442/push",
    false,
    stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/plain\r\n",
        'content' => "bix/car_wristband:new fall"
    ]])
);

@file_get_contents(
    "http://127.0.0.1:6442/push",
    false,
    stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/plain\r\n",
        //'content' => "bix/goto:goto7.65|-1.15"
        'content' => "bix/goto:goto" . $positions[(int)$_REQUEST["zone"] - 1][$sens][0] . "|" . $positions[(int)$_REQUEST["zone"] - 1][$sens][1] . "|" . $dir
    ]])
);

$updateQuery = "UPDATE `bix_state` 
    SET
        `last_goto`=:g
    WHERE 1";
$updateStmt = $db->prepare($updateQuery);
$updateStmt->execute([
    "g" => $positions[(int)$_REQUEST["zone"] - 1][$sens][0] . "|" . $positions[(int)$_REQUEST["zone"] - 1][$sens][1] . "|" . $dir
]);

header("Location: ./ok");
exit;

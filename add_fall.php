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
    "20.56|8.54|0.696", // (proche porte sortie)
    "14.68|4.19|0.696",
    "8.58|-0.3|0.696", // zone 3
    "4.13|-3.58|0.696" // zone 4 (proche hall)
];

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
	'content' => "bix/goto:goto" . $positions[$_REQUEST["zone"] - 1]
    ]])
);

header("Location: ./ok");
exit;

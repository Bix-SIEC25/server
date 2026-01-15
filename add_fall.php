<?php

if (!isset($_REQUEST["devid"]) || !isset($_REQUEST["mag"]) || !isset($_REQUEST["zone"])) {
    header("Location: ./ko");
    exit;
}

include_once("../insa_db.php");
$db = dbConnect();

$sqlQuery = 'INSERT INTO fall_alerts(device_id, jerkmagnitude, zone) VALUES (:dev, :mag, :zone)';

$insert = $db->prepare($sqlQuery);
$insert->execute([
    'dev' => htmlspecialchars($_REQUEST["devid"]),
    'mag' => htmlspecialchars($_REQUEST["mag"]),
    'zone' => htmlspecialchars($_REQUEST["zone"])
]);

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

header("Location: ./ok");
exit;


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
	'content' => "bix/goto:goto13.3|3.04"
    ]])
);

header("Location: ./ok");
exit;

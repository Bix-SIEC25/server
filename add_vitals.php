<?php

if (!isset($_REQUEST["devid"]) || !isset($_REQUEST["o"]) || !isset($_REQUEST["h"]) || !isset($_REQUEST["t"])) {
    header("Location: ./ko");
    exit;
}

include_once("../insa_db.php");
$db = dbConnect();
include_once("scenario_mark.php");

$sqlQuery = 'INSERT INTO residents_vitals(device_id, spo2, heart_rate, temperature) VALUES (:dev, :spo2, :heartrate, :temperature)';

$insert = $db->prepare($sqlQuery);
$insert->execute([
    'dev' => htmlspecialchars($_REQUEST["devid"]),
    'spo2' => htmlspecialchars($_REQUEST["o"]),
    'heartrate' => htmlspecialchars($_REQUEST["h"]),
    'temperature' => htmlspecialchars($_REQUEST["t"])
]);

@file_get_contents(
    "http://127.0.0.1:6442/push",
    false,
    stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/plain\r\n",
        'content' => "bix/wristband:new vitals"
    ]])
);

header("Location: ./ok");
exit;

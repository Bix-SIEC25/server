<?php

if (!isset($_REQUEST["devid"]) || !isset($_REQUEST["mag"])) {
    header("Location: ./ko");
    exit;
}

include_once("../insa_db.php");
$db = dbConnect();

$sqlQuery = 'INSERT INTO fall_alerts(device_id, jerkmagnitude) VALUES (:dev, :mag)';

$insert = $db->prepare($sqlQuery);
$insert->execute([
    'dev' => htmlspecialchars($_REQUEST["devid"]),
    'mag' => htmlspecialchars($_REQUEST["mag"])
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

header("Location: ./ok");
exit;

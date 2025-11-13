<?php

if (!isset($_REQUEST["devid"]) || !isset($_REQUEST["mag"])) {
    header("Location: ./ko");
    exit;
}

include_once("../db_insa.php");
$db = dbConnect();

$sqlQuery = 'INSERT INTO fall_alerts(device_id, jerkmagnitude) VALUES (:dev, :mag)';

$insert = $db->prepare($sqlQuery);
$insert->execute([
    'dev' => htmlspecialchars($_REQUEST["devid"]),
    'mag' => htmlspecialchars($_REQUEST["mag"])
]);

header("Location: ./ok");
exit;

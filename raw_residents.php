[
<?php

include_once("../insa_db.php");
$db = dbConnect();

$residents = [];
$stmt = $db->prepare('SELECT * FROM residents');
$stmt->execute();
$residents = $stmt->fetchAll();


foreach ($residents as $resident_index => $resident) {
    if ($resident_index)
        echo ",";
    echo sprintf("{\"name\":\"%s\",\"device_id\":%d}", $resident["name"], $resident["device_id"]);
    // echo sprintf("\"%s\":{\"name\":\"%s\",\"device_id\":%d}", $resident["name"], $resident["name"], $resident["device_id"]);
}

?>
]
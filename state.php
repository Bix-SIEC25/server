<?php
include_once("../insa_db.php");
$db = dbConnect();

$stmt = $db->prepare("SELECT scenario_name, x, y, dir, wait_car, qr, face, dialog, fall_ia, mov_car, wait_image_verif FROM bix_state");
$stmt->execute([]);
$res = $stmt->fetchAll();
$data = [];
if (sizeof($res) == 1) {
    // $data = $res[0];
    $data["scenario_name"] = $res[0]["scenario_name"];
    $data["x"] = $res[0]["x"];
    $data["y"] = $res[0]["y"];
    $data["dir"] = $res[0]["dir"];
    $data["wait_car"] = $res[0]["wait_car"];
    $data["qr"] = $res[0]["qr"];
    $data["face"] = $res[0]["face"];
    $data["dialog"] = $res[0]["dialog"];
    $data["fall_ia"] = $res[0]["fall_ia"];
    $data["mov_car"] = $res[0]["mov_car"];
    $data["wait_image_verif"] = $res[0]["wait_image_verif"];
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);

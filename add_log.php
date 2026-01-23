<?php

if (!isset($_REQUEST["sender"]) || !isset($_REQUEST["type"]) || !isset($_REQUEST["msg"])) { // || str_ends_with(htmlspecialchars($_REQUEST["sender"]),"watchdog")) {
    header("Location: ./ko");
    exit;
}

include_once("../insa_db.php");
$db = dbConnect();
include_once("scenario_mark.php");

$sqlQuery = 'INSERT INTO bix_logs(sender, type, message) VALUES (:sender, :type, :msg)';

$sender = htmlspecialchars($_REQUEST["sender"]);
$type = htmlspecialchars($_REQUEST["type"]);
$msg = htmlspecialchars($_REQUEST["msg"]);

$insert = $db->prepare($sqlQuery);
$insert->execute([
    'sender' => $sender,
    'type' => $type,
    'msg' => $msg
]);

// updateScenario($db, "giovfall", $msg == "confirmed" && $sender == "state", "Started");

if ($sender == "FaceRecognitionNode" || $sender == "QRNode") {
    copy("uploads/last.jpg", "uploads/visu_fall.jpg");
    @file_get_contents(
        "http://127.0.0.1:6442/push",
        false,
        stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Content-Type: text/plain\r\n",
            'content' => "bix/admin:visu_fall|$msg"
        ]])
    );
    updateScenario($db, "giovfall", $msg == "Giovanna", "Giovanna detected");
    updateScenario($db, "hugofake", $msg == "Hugo", "Hugo detected");

}

if ($sender == "goto" && str_contains($msg, "Arrival flag")) {
    @file_get_contents(
        "http://127.0.0.1:6442/push",
        false,
        stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Content-Type: text/plain\r\n",
            'content' => "bix/admin:endgoto"
        ]])
    );

    $updateQuery = "UPDATE `bix_state` 
    SET
        `last_goto`=:g
    WHERE 1";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([
        "g" => ""
    ]);

    updateScenario($db, "hugofake", true, "Arrived");
}

@file_get_contents(
    "http://127.0.0.1:6442/push",
    false,
    stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/plain\r\n",
        'content' => "bix/logs:new log"
    ]])
);

@file_get_contents(
    "http://127.0.0.1:6442/push",
    false,
    stream_context_create(['http' => [
        'method' => 'POST',
        'header' => "Content-Type: text/plain\r\n",
        'content' => "bix/log_" . $sender . ":" . $type . "|" . $msg
    ]])
);

updateScenario($db, "", $msg == "patrolling" && $sender == "state", "Started");
updateScenario($db, "giovfall", $msg == "noemergency" && $sender == "state", "No emergency");
// updateScenario($db, "", $msg == "emergency" && $sender == "state", "Started"); 
updateScenario($db, "hugofake", $sender == "socket" && $msg == "Image verification: false", "Denied");
updateScenario($db, "giovfall", $sender == "socket" && $msg == "Image verification: true", "Confirmed");


header("Location: ./ok");
exit;

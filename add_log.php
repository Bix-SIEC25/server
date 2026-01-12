<?php

if (!isset($_REQUEST["sender"]) || !isset($_REQUEST["type"]) || !isset($_REQUEST["msg"])) { // || str_ends_with(htmlspecialchars($_REQUEST["sender"]),"watchdog")) {
    header("Location: ./ko");
    exit;
}

include_once("../insa_db.php");
$db = dbConnect();

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

if ($sender == "FaceRecognitionNode" || $sender == "QRNode") {
    copy("uploads/last.jpg","uploads/recog.jpg");
    @file_get_contents(
        "http://127.0.0.1:6442/push",
        false,
        stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Content-Type: text/plain\r\n",
            'content' => "bix/admin:visu_fall|$msg"
        ]])
    );
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

header("Location: ./ok");
exit;

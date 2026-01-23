<?php

if (!isset($_REQUEST["m"]) && !isset($_REQUEST["s"], $_REQUEST["name"])) {
    header("Location: ./ko");
    exit;
}

// https://bix.ovh/scenario_set?name=first&s=[{%22step%22:%221%22},{%22transition%22:%221-%3E2%22},{%22step%22:%222%22},{%22transition%22:%222-%3E3%22},{%22step%22:%223%22},{%22transition%22:%223-%3E4%22},{%22step%22:%224%22},{%22transition%22:%224-%3E5%22},{%22step%22:%225%22}]
// https://bix.ovh/scenario_set?name=giovfall&s=[{"step":"Started"},{"transition":"patrolling"},{"step":"Fall via cam"},{"transition":"recognizing face"},{"step":"Giovanna detected"},{"transition":"Waiting confirmation"},{"step":"Confirmed"},{"transition":"Asking questions"},{"step":"No emergency"}]
// https://bix.ovh/scenario_set?name=hugofake&s=[{"step":"Started"},{"transition":"patrolling"},{"step":"Fake necklace fall"},{"transition":"going to fall"},{"step":"Arrived"},{"transition":"Detecting QRCode"},{"step":"Hugo detected"},{"transition":"Waiting confirmation"},{"step":"Denied"}]

// set scenario case
if (isset($_REQUEST["s"], $_REQUEST["name"])) {

    $s = urldecode($_REQUEST["s"]);
    $name = htmlspecialchars($_REQUEST["name"]);

    $scenario = json_decode($s, true);
    foreach ($scenario as $index => $state) {
        if ($index == 0) {
            $scenario[$index]['state'] = 'next';
        } else {
            $scenario[$index]['state'] = 'todo';
        }
    }
    $s = json_encode($scenario);

    include_once("../insa_db.php");
    $db = dbConnect();
    $updateQuery = "UPDATE `bix_state` 
    SET
        `scenario`=:s,
        `scenario_name`=:name,
        `last_step`=''
    WHERE 1";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([
        "s" => $s,
        "name" => $name
    ]);

    @file_get_contents(
        "http://127.0.0.1:6442/push",
        false,
        stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Content-Type: text/plain\r\n",
            'content' => "bix/admin:setScenario:" . $s
        ]])
    );
}

header("Location: ./ok");
exit;

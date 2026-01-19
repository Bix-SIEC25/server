<?php

if (!isset($_REQUEST["m"]) && !isset($_REQUEST["s"])) {
    header("Location: ./ko");
    exit;
}

// set scenario case
if (isset($_REQUEST["s"])) {

    $s = htmlspecialchars($_REQUEST["s"]);

    include_once("../insa_db.php");
    $db = dbConnect();
    $updateQuery = "UPDATE `bix_state` 
    SET
        `scenario`=:s,
        `last_step`=''
    WHERE 1";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([
        "s" => $s
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

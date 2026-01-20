<?php

if (!isset($_REQUEST["m"])) {
    header("Location: ./ko");
    exit;
}

include_once("../insa_db.php");
$db = dbConnect();
include_once("scenario_mark.php");
updateScenario($db, "", true, htmlspecialchars($_REQUEST["m"]));


header("Location: ./ok");
exit;
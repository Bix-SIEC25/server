<?php session_start();
$_SESSION["connected"] = false;

header("Location: ./ok");
exit;
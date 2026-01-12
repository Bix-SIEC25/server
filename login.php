<?php session_start();

if (isset($_REQUEST["pw"]) && htmlspecialchars($_REQUEST["pw"]) == "geicar") {
    $_SESSION["connected"] = true;

    header("Location: ./ok");
    exit;
} else  {
    $_SESSION["connected"] = false;

    header("Location: ./ko");
    exit;
}
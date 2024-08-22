<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;

if ($profile['id'] !== json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true)['oauth']['admin']) {
    header("Location: /");
    die();
}

if (!isset($_GET["id"]) || str_contains($_GET["id"], ".") || str_contains($_GET["id"], "/") || !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $_GET["id"])) die();
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $_GET["id"] . "/reviewed.txt", "");

header("Location: /admin");
die();
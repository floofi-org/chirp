<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;

if (!isset($_GET["id"]) || str_contains($_GET["id"], ".") || str_contains($_GET["id"], "/") || !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $_GET["id"])) die();
$item = $_GET["id"];
if (!(file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt")) || file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") !== $profile["id"]) die();

file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $_GET["id"] . "/removed.txt", "");
die();
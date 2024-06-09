<?php

if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] !== "https://sunnystarbot.equestria.dev/app/") die();

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/updates.php";

header("Content-Type: application/json");
header('Cache-Control: no-cache');

die(json_encode(getList(), JSON_PRETTY_PRINT));
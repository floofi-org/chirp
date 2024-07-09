<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/updates.php";

endpoint();

output([
    "available" => getPossible()
]);

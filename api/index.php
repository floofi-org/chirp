<?php

if ($_SERVER['HTTP_X_FORWARDED_HOST'] !== "api.sunnystarbot.equestria.dev") {
    header("Location: /");
    die();
}

header("Location: https://sunnystarbot.equestria.dev/docs");
die();
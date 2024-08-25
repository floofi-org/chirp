<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;

if ($profile['id'] !== json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true)['oauth']['admin']) {
    header("Location: /");
    die();
}

$userDataCache = [];

function getUserData($id) {
    global $userDataCache;
    if (isset($userDataCache[$id])) return $userDataCache[$id];

    foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens"), function ($i) { return !str_starts_with($i, "."); }) as $token) {
        $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . $token), true);

        if ($data["id"] === $id) {
            $userDataCache[$id] = $data;
        }
    }

    if (isset($userDataCache[$id])) return $userDataCache[$id];
    return null;
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Moderation center | Floofi Voice Generator</title>
    <link href="/assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <script src="/assets/bootstrap/bootstrap.min.js"></script>
    <link rel="shortcut icon" href="https://voice.floo.fi/assets/favicon.png" type="image/svg+xml">
</head>
<body>
<div class="container">
    <br><br>
    <h1>History</h1>

    <div class="list-group">
        <?php

        $list = array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs"), function ($i) { return !str_starts_with($i, "."); });

        usort($list, function ($a, $b) {
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $a . "/reviewed.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $b . "/reviewed.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $a . "/timestamp.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $b . "/timestamp.txt")) {
                return (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $b . "/timestamp.txt") - (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $a . "/timestamp.txt");
            } else {
                return -INF;
            }
        });

        foreach ($list as $item) { if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/reviewed.txt")) { ?>
            <div class="list-group-item" style="display: grid; grid-template-columns: .3fr 1.5fr 4fr 1.2fr; grid-gap: 10px;">
                <div>
                    <code>
                        <?php

                        $text = file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/audio.wav") ? "P" : "-";
                        $text .= !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") ? "W" : "-";
                        $text .= (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") && !file_exists("/proc/" . trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt")))) ? "C" : "-";
                        $text .= file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/held.txt") ? "F" : "-";
                        $text .= file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt") ? "B" : "-";

                        echo($text);

                        ?>
                    </code>
                </div>
                <div title="<?= trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt")) ?>" onclick="navigator.clipboard.writeText('<?= trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt")) ?>'); alert('Copied user ID (<?= trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt")) ?>) to clipboard');"><?php

                    $user = getUserData(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt")));

                    if (isset($user)): ?>
                        <div><?= str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", $user["name"]))) ?></div>
                        <div class="text-muted"><?= $user["profile"]["email"]["email"] ?></div>
                        <div class="text-muted"><?php

                        $models = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/models.json"), true);
                        $m = file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/model.txt") ? trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/model.txt")) : "sunny";
                        if (isset($models[$m])) {
                            echo($models[$m]["name"]);
                        } else {
                            echo($m);
                        }

                        ?></div>
                    <?php else: ?>
                        <code><?= $user["id"] ?></code>
                    <?php endif; ?></div>
                <div <?= file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt") ? (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/held.txt") ? 'class="text-danger"' : 'class="text-warning') : '' ?>><?= str_replace("<", "&lt;", str_replace(">", "&gt;", str_replace("&", "&amp;", file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt")))) ?></div>
                <div>
                    <?= date('Y-m-d G:i:s e', (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")) ?><br>
                    <a href="/admin/reviewed.php?id=<?= $item ?>">Mark as reviewed</a> · <a href="https://cdn.equestria.dev/sunnystarbot/content/<?= $item ?>/audio.wav" target="_blank">+</a>
                </div>
            </div>
        <?php }} ?>

        <p class="text-muted small mt-3">P = Processed<br>W = Working<br>C = Crashed<br>F = Filtered<br>B = Blocked</p>

        <br><br>
    </div>
</div>
</html>

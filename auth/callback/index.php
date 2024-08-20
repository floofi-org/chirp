<?php

$app = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true);
$server = "account.equestria.dev";

function generateToken(): string {
    return "ssb-" . str_replace("/", ".", base64_encode(random_bytes(96)));
}

function generateHandoff(): string {
    return str_replace("/", ".", base64_encode(random_bytes(32)));
}

header("Content-Type: text/plain");

if (!isset($_GET['code'])) {
    header("Location: /auth/init");
    die();
}

$appdata = $app;

$crl = curl_init('https://' . $server . '/hub/api/rest/oauth2/token');
curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($crl, CURLINFO_HEADER_OUT, true);
curl_setopt($crl, CURLOPT_POST, true);
curl_setopt($crl, CURLOPT_HTTPHEADER, [
    "Authorization: Basic " . base64_encode($appdata["oauth"]["id"] . ":" . $appdata["oauth"]["secret"]),
    "Content-Type: application/x-www-form-urlencoded",
    "Accept: application/json"
]);
curl_setopt($crl, CURLOPT_POSTFIELDS, "grant_type=authorization_code&redirect_uri=" . urlencode("https://voice-api.floo.fi/auth/callback") . "&code=" . $_GET['code']);

$result = curl_exec($crl);
$result = json_decode($result, true);

curl_close($crl);

if (isset($result["access_token"])) {
    $crl = curl_init('https://' . $server . '/hub/api/rest/users/me');
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($crl, CURLINFO_HEADER_OUT, true);
    curl_setopt($crl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $result["access_token"],
        "Accept: application/json"
    ]);

    $result = $result_orig = curl_exec($crl);
    $result = json_decode($result, true);

    if ($appdata["acl"] && !in_array($result["id"], $appdata["oauth"]["allowed"])) {
        header("Location: /banned/?user=" . rawurlencode(base64_encode($result["login"] . " (" . $result["profile"]["email"]["email"] . ")")));
        die();
    }

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/handoff")) mkdir($_SERVER['DOCUMENT_ROOT'] . "/includes/handoff");

    $token = generateToken();
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . $token, $result_orig);
    header("Set-Cookie: SSB_SESSION_TOKEN=" . $token . "; SameSite=None; Path=/; Secure; HttpOnly; SameSite=None; Expires=" . date("r", time() + (86400 * 730)));
    setcookie("_callback", "", 0, "/");
    $handoff = generateHandoff();
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/handoff/" . $handoff, json_encode([
        "token" => $token,
        "date" => date('c')
    ]));

    $callback = $_COOKIE['_callback'] ?? "https://voice.floo.fi";

    if (str_starts_with($callback, "https:")) {
        unlink($_SERVER['DOCUMENT_ROOT'] . "/includes/handoff/" . $handoff);
        header("Location: $callback/app");
    } else {
        header("Location: $callback/handoff#" . $handoff);
    }

    die();
} else {
    header("Location: /auth/init");
    die();
}

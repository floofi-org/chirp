<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;

$keys = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/keys.json"), true);

function getAPIKey(): string {
    global $profile;
    global $keys;

    if (!isset($keys[$profile["id"]])) {
        $keys[$profile["id"]] = substr(str_replace("+", ".", str_replace("/", "_", str_replace("=", "", base64_encode(random_bytes(64))))), 0, 48);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/keys.json", json_encode($keys));
    }

    return $keys[$profile["id"]];
}

?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>API docs (v2) | Floofi Voice Generator</title>
    <link href="/assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <script src="/assets/bootstrap/bootstrap.min.js"></script>
    <link rel="shortcut icon" href="https://voice.floo.fi/assets/favicon.png" type="image/svg+xml">
    <style>
        #api-key {
            color: transparent;
            background-color: black;
        }

        #api-key:hover {
            background-color: transparent;
            color: inherit;
        }
    </style>
</head>
<body>
<div class="container">
    <br><br>
    <h1>Floofi Voice Generator API docs <span class="badge bg-secondary">v2</span></h1>

    <p>Voice Generator provides a RESTful API that you can use to interact with various features of the AI.</p>

    <h3>API limits</h3>
    <ul>
        <li>All API requests must be authenticated, either with an interactive session or an API key. To get your API key, please check below.</li>
        <li>A single user is limited to a generation at once.</li>
        <li>Users making an abusive number of requests will get their account blocked.</li>
        <li>Remember to follow the Acceptable Use Policy for Voice Generator, namely to properly credit your use of the platform as "Floofi Voice Generator" or "Floofi."</li>
    </ul>

    <h3>Your API key</h3>
    <p>Your API key is <b>strictly personal</b>, sharing it will allow anyone to use Voice Generator on your behalf. It is displayed below, and you can hover over it or tap it to display it.</p>
    <ul>
        <li><code id="api-key"><?= getAPIKey() ?></code> · <a onclick="navigator.clipboard.writeText(document.getElementById('api-key').innerText.trim()); return false;" href="#">Copy</a>, <a onclick="return confirm('Are you sure? If you continue, you will need to change your API key in any application that is using it.');" href="/server/docser/docs/reset.php">Reset</a></li>
    </ul>
    <p>To authenticate with the API, you need to set the <code>Authorization</code> header to <code>Bearer &lt;API key&gt;</code>. If you are logged into  through the website, your session will work on the API, although this is not a recommended way to use the API. If you are using Insomnia, click on the "Auth" tab and select "Bearer Token."</p>
    <p>Interactive authentication (for use with a web client) is done through the /auth/init and /auth/callback endpoints with OAuth2 authentication using Floofi ID. Note that CORS is used to prevent cross-site request forgery and that not all origins are able to use the API directly.</p>

    <h3>Output data</h3>
    <pre>
error?: {
    code: number,
    name: string,
    see: "https://<?= $_SERVER['HTTP_HOST'] ?>/docs/"
}
output: any|null
    </pre>

    <h3>Error codes</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Meaning</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>400</td>
                <td>Bad Request</td>
                <td>A required argument for this endpoint is missing or incorrectly formatted.</td>
            </tr>
            <tr>
                <td>401</td>
                <td>Unauthorized</td>
                <td>No API key was provided with the request. If an API key was provided, it is not valid.</td>
            </tr>
            <tr>
                <td>403</td>
                <td>Forbidden</td>
                <td>You should normally have access to this endpoint, but your account has been blocked by the administrators.</td>
            </tr>
            <tr>
                <td>404</td>
                <td>Not Found</td>
                <td>The requested API endpoint does not exist or is not supported in this version of the API.</td>
            </tr>
            <tr>
                <td>405</td>
                <td>Method Not Allowed</td>
                <td>The HTTP method used at this endpoint is not the one that is expected.</td>
            </tr>
            <tr>
                <td>409</td>
                <td>Conflict</td>
                <td>You have tried to change the state of an item to a state it is already in.</td>
            </tr>
            <tr>
                <td>413</td>
                <td>Payload Too Large</td>
                <td>One of the parameters sent to this API endpoint is longer than the allowed length.</td>
            </tr>
            <tr>
                <td>429</td>
                <td>Too Many Requests</td>
                <td>This API key has exceeded the number of allowed requests in a certain timespan.</td>
            </tr>
            <tr>
                <td>451</td>
                <td>Unavailable For Legal Reasons</td>
                <td>The request goes against 's content filters and/or terms of use.</td>
            </tr>
            <tr>
                <td>500</td>
                <td>Internal Server Error</td>
                <td>A server error has occurred while processing this request.</td>
            </tr>
            <tr>
                <td>501</td>
                <td>Not Implemented</td>
                <td>The AI model you are trying to use does not exist.</td>
            </tr>
        </tbody>
    </table>

    <h3>Endpoints</h3>
    <p>All API endpoints take root in https://<?= $_SERVER['HTTP_HOST'] ?>/api.</p>
    <table class="table">
        <thead>
        <tr>
            <th>Method</th>
            <th>Path</th>
            <th>Parameters</th>
            <th>Description</th>
            <th>Output</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><code>GET</code></td>
            <td>v2/models</td>
            <td>-</td>
            <td>Get the available models.</td>
            <td><pre>[
    {
        "id": string,
        "name": string,
        "source": string,
        "version": string
    }
]</pre></td>
        </tr>
        <tr>
            <td><code>GET</code></td>
            <td>v2/status</td>
            <td>-</td>
            <td>Get system information.</td>
            <td><pre>{
    "user": string,
    "processingUnits": [
        {
            "id": string,
            "pid": number,
            "busy": boolean
        }
    ]
}</pre></td>
        </tr>
        <tr>
            <td><code>GET</code></td>
            <td>v2/available</td>
            <td>-</td>
            <td>Check if enqueing a new generation is possible.</td>
            <td><pre>{
    "available": boolean
}</pre></td>
        </tr>
        <tr>
            <td><code>GET</code></td>
            <td>v2/history</td>
            <td>URL: <code>amount</code>: number (optional)</td>
            <td>Get previous generations associated with this account.</td>
            <td><pre>[
    {
        "id": string,
        "version": string,
        "author": string,
        "status": generation-status,
        "input": string,
        "filename": string,
        "audio_url": string?,
        "explicit": boolean,
        "model": string
    }
]</pre></td>
        </tr>
        <tr>
            <td><code>GET</code></td>
            <td>v2/history/<b>:id</b></td>
            <td>-</td>
            <td>Get a specific generation from its ID.</td>
            <td><pre>{
    "id": string,
    "version": string,
    "author": string,
    "status": generation-status,
    "input": string,
    "filename": string,
    "audio_url": string?,
    "explicit": boolean,
    "model": string
}</pre></td>
        </tr>
        <tr>
            <td><code>DELETE</code></td>
            <td>v2/history/<b>:id</b></td>
            <td>-</td>
            <td>Remove a specific generation from your history using its ID.</td>
            <td><pre>{
    "id": string,
    "version": string,
    "author": string,
    "status": "removed",
    "input": string,
    "filename": string,
    "audio_url": null,
    "explicit": boolean,
    "model": string
}</pre></td>
        </tr>
        <tr>
            <td><code>POST</code></td>
            <td>v2/generate</td>
            <td>JSON: <code>input</code>: string<br>JSON: <code>model</code>: string</td>
            <td>Enqueue a new generation job.</td>
            <td><pre>{
    "id": string
}</pre></td>
        </tr>
        <tr>
            <td><code>POST</code></td>
            <td>v2/handoff</td>
            <td>JSON <code>token</code>: string</td>
            <td>Get an interactive access token from a client handoff token. Used only during development.</td>
            <td><pre>{
    "token": string
}</pre></td>
        </tr>
        </tbody>
    </table>

    <h3>Types</h3>
    <h5>generation-status</h5>
    <pre>"removed"|"processed"|"generating"|"queued"|"crashed"</pre>

    <br><br><br>
</div>
</html>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sunny Starbot</title>
    <script src="https://www.google.com/recaptcha/api.js?render=<?= json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true)["recaptcha"]["site"] ?>"></script>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
    <script src="/assets/bootstrap.min.js"></script>
    <style>
        .grecaptcha-badge { visibility: hidden; }
        #mobile-separator { display: none; }

        @font-face {
            src: url("/assets/MLPFindYourSparkle.otf");
            font-family: "MLPFindYourSparkle";
        }

        @font-face {
            src: url("/assets/Jost-VariableFont_wght.ttf");
            font-family: "Jost";
            font-style: normal;
        }

        @font-face {
            src: url("/assets/Jost-Italic-VariableFont_wght.ttf");
            font-family: "Jost";
            font-style: italic;
        }

        #bottom, #bottom * {
            color: white !important;
        }

        *, .modal-title {
            font-family: Jost, -apple-system, system-ui, "Segoe UI", "Noto Sans", sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: MLPFindYourSparkle, Jost, -apple-system, system-ui, "Segoe UI", "Noto Sans", sans-serif;
        }


        @media (max-width: 992px) {
            #mobile-separator { display: block; }
            #panes { grid-template-columns: 1fr !important; grid-gap: 0 !important; }
            #pane-left, #pane-right { order: 0 !important; }
        }

        .list-group, .list-group-item {
            border: none !important;
            border-radius: 0;
        }

        .list-group-item {
            border-radius: 10px;
        }

        .list-group-item:nth-child(1) {
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
        }

        .list-group-item:nth-last-child(1) {
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            margin-bottom: 0 !important;
        }

        .list-group-item {
            background-color: rgba(255, 255, 255, .25);
            color: white;
            padding: 20px;
            margin-bottom: 10px;
        }

        main .text-muted {
            color: rgba(255, 255, 255, .75) !important;
        }

        textarea.form-control {
            border-radius: 30px !important;
            padding: 20px !important;
        }

        textarea.form-control[disabled], textarea.form-control {
            background-color: rgba(255, 255, 255, .25) !important;
            border: none !important;
        }

        textarea.form-control[disabled] {
            opacity: .5;
        }

        textarea.form-control, textarea.form-control::placeholder {
            color: white !important;
        }

        textarea.form-control::placeholder {
            opacity: .5;
        }

        .form-control:focus {
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(204, 39, 196, 0.5);
        }

        .btn-primary {
            border-radius: 30px !important;
            border: none !important;
            padding: 10px 20px !important;
            background-color: rgba(255, 255, 255, .25) !important;
        }

        .btn-primary:hover {
            background-color: rgba(255, 255, 255, .15) !important;
        }

        .btn-primary:active, .btn-primary:focus {
            background-color: rgba(255, 255, 255, .1) !important;
            box-shadow: 0 0 0 0.25rem rgba(204, 39, 196, 0.5) !important;
        }

        @keyframes pulse {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .modal {
            backdrop-filter: blur(30px) !important;
            -webkit-backdrop-filter: blur(30px) !important;
        }

        .modal-content {
            background-color: #9c1d96 !important;
            color: white !important;
            border: none !important;
            border-radius: 30px !important;
        }

        .modal-header {
            border: none !important;
            padding: 20px !important;
        }

        .modal-content .btn-close {
            filter: invert(1);
        }

        .modal-body .alert:nth-last-child(1) {
            margin-bottom: 0 !important;
        }

        .alert {
            border-radius: 30px;
            padding: 20px;
            background-color: #691061 !important;
            color: white !important;
            border: none !important;
        }

        .history-player-action:hover img {
            background-color: rgba(0, 0, 0, .1);
        }

        .history-player-action:active img {
            background-color: rgba(0, 0, 0, .25);
        }

        .explicit {
            filter: blur(10px);
        }

        .explicit:hover, .explicit:active {
            filter: none !important;
        }
    </style>
    <link rel="shortcut icon" href="/assets/favicon.svg" type="image/svg+xml">
</head>
<body style="background-image: url('/assets/bg.png'); background-size: cover; background-position: center; background-attachment: fixed; background-color: #feaf91;">
<br><br>

<main class="container" style="border: 5px solid #9c1d96; background-color: #9c1d96; color: white; border-radius: 30px; padding: 30px;">
    <img src="/assets/banner.png" alt="Sunny Starbot" style="width: 100%; max-width: 768px; display: block; margin-left: auto; margin-right: auto; margin-bottom: 30px;">

    <div style="display: grid; grid-template-columns: 1fr 1fr; grid-gap: 30px;" id="panes">
        <div id="pane-right" style="order: 2;">
            <h2 style="margin-bottom: 30px;">Generate something</h2>

            <form onsubmit="sendRequest(); return false;">
                <textarea class="form-control" rows="3" style="resize: none;" id="input" autofocus placeholder="Enter text here." maxlength=160" disabled></textarea>

                <div style="margin-top: 1rem; display: grid; grid-template-columns: 1fr max-content; grid-gap: 10px;">
                    <div style="align-items: center; display: flex; align-items: center; justify-content: center;" class="text-muted">
                        <div>
                            <p>Your input will be read by real people, never enter personal or confidential information.</p>Version <?= trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/version-plus.txt")) ?> · <a style="color: rgba(255, 255, 255, .75);" href="/docs/">API docs</a>
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-primary" id="submit-btn" disabled>Generate</button>
                    </div>
                </div>

                <div style="margin-top: 20px; position: absolute; border: 2px solid rgba(255, 255, 255, .25); border-radius: 30px; padding: 20px;">
                    <ol style="padding-left: 20px; margin: 0;">
                        <li>Credit "Sunny Starbot"</li>
                        <li>Don't mix with other TTS</li>
                        <li>Keep it family-friendly</li>
                    </ol>
                </div>

                <img src="/assets/sunny.png" style="max-width: 75%; margin-left: auto; display: block; margin-right: -35px;">

                <script>
                    window.processing = false;

                    function checkPossible() {
                        let possible = window.possibleData;

                        if (!processing && possible && document.getElementById("submit-btn").disabled) {
                            document.getElementById("submit-btn").disabled = false;
                            document.getElementById("input").disabled = false;
                            if (!modal || !modal._isShown) document.getElementById("input").focus();
                        } else if ((!possible || processing) && !document.getElementById("submit-btn").disabled) {
                            document.getElementById("submit-btn").disabled = true;
                            document.getElementById("input").disabled = true;
                        }
                    }

                    document.getElementById("input").onkeydown = (event) => {
                        if (event.code === "Enter" && (event.metaKey || event.ctrlKey)) {
                            sendRequest();
                        }
                    }

                    function sendRequest() {
                        if (document.getElementById("input").value.trim() !== "") {
                            window.processing = true;
                            document.getElementById("submit-btn").disabled = true;
                            document.getElementById("input").disabled = true;

                            grecaptcha.ready(function() {
                                grecaptcha.execute('<?= json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true)["recaptcha"]["site"] ?>', {action: 'submit'}).then((token) => {
                                    fetch("/app/enqueue.php?text=" + encodeURIComponent(document.getElementById("input").value) + "&token=" + encodeURIComponent(token)).then((res) => {
                                        res.json().then((id) => {
                                            window.processing = false;

                                            if (typeof id !== "boolean") {
                                                document.getElementById("input").value = "";

                                                fetch("/app/list.php").then((res) => {
                                                    res.json().then((data) => {
                                                        window.listData = data;
                                                        refreshList();
                                                    });
                                                });
                                            } else if (id === true) {
                                                document.getElementById("submit-btn").disabled = false;
                                                document.getElementById("input").disabled = false;
                                                modal2.show();
                                            } else if (id === false) {
                                                document.getElementById("submit-btn").disabled = false;
                                                document.getElementById("input").disabled = false;
                                                modal.show();
                                            }
                                        });
                                    });
                                });
                            });
                        }
                    }

                    function timeAgo(time) {
                        if (!isNaN(parseInt(time))) {
                            time = new Date(time).getTime();
                        }

                        let periods = ["second", "minute", "hour", "day", "week", "month", "year", "age"];

                        let lengths = ["60", "60", "24", "7", "4.35", "12", "100"];

                        let now = new Date().getTime();

                        let difference = Math.round((now - time) / 1000);
                        let tense;
                        let period;

                        if (difference <= 10 && difference >= 0) {
                            return "now";
                        } else if (difference > 0) {
                            tense = "ago";
                        } else {
                            tense = "later";
                        }

                        let j;

                        for (j = 0; difference >= lengths[j] && j < lengths.length - 1; j++) {
                            difference /= lengths[j];
                        }

                        difference = Math.round(difference);

                        period = periods[j];

                        return `${difference} ${period}${difference > 1 ? "s" : ""} ${tense}`;
                    }

                    function playerAction(id) {
                        let audio = document.getElementById("history-player-" + id);
                        let btn = document.getElementById("history-player-" + id + "-action");
                        let btnPlay = document.getElementById("history-player-" + id + "-play");
                        let btnStop = document.getElementById("history-player-" + id + "-stop");

                        if (audio.paused) {
                            audio.currentTime = 0;
                            audio.muted = false;
                            audio.volume = 1;
                            audio.play();

                            btn.title = "Stop";
                            btnPlay.style.display = "none";
                            btnStop.style.display = "inline-block";
                        } else {
                            audio.currentTime = 0;
                            audio.muted = false;
                            audio.volume = 1;
                            audio.pause();

                            btn.title = "Play";
                            btnPlay.style.display = "inline-block";
                            btnStop.style.display = "none";
                        }
                    }

                    window.deleted = [];

                    function playerDelete(id) {
                        document.getElementById("history-" + id).outerHTML = "";

                        fetch("/app/remove.php?id=" + id).then(() => {
                            fetch("/app/list.php").then((res) => {
                                res.json().then((data) => {
                                    window.listData = data;
                                    window.lastList = data;
                                    refreshList();
                                });
                            });
                        });
                    }

                    function playerDownload(id, name) {
                        let element = document.createElement("a");
                        element.setAttribute("download", name);
                        element.setAttribute("href", "https://cdn.equestria.dev/sunnystarbot/content/" + id + "/audio.wav");
                        element.click();
                    }

                    function refreshList() {
                        let data = window.listData;

                        if (data.length > 0) {
                            document.getElementById("list-message").style.display = "none";
                            document.getElementById("list").style.display = "";

                            for (let id of Array.from(document.getElementById("list").children).map(i => i.id.split("-")[1])) {
                                if (!data.map(i => i.id).includes(id)) {
                                    document.getElementById("history-" + id).outerHTML = "";
                                }
                            }

                            let index = 0;

                            for (let item of data) {
                                if (index <= 29) {
                                    if (!document.getElementById("history-" + item.id)) {
                                        document.getElementById("list").insertAdjacentHTML(index === 0 ? "afterbegin" : "beforeend", `
                                                <div id="history-${item.id}" class="list-group-item">
                                                    <div style="display: grid; grid-template-columns: 3fr 2.5fr; margin-bottom: 10px;">
                                                        <div class="history-prompt" style="white-space: nowrap;overflow: hidden !important;text-overflow: ellipsis;" title="${item.input.replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll('"', "&quot;")}"><b>${item.input.replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;")}</b></div>
                                                        <div class="history-time text-muted" style="text-align: right;">${timeAgo(item.time)} · ${item.version}</div>
                                                    </div>
                                                    <div class="history-player" style="display: none; height: 32px;">
                                                        <audio id="history-player-${item.id}"></audio>
                                                        <div style="height: 32px; background-color: #b14eab; border-radius: 999px; display: grid; grid-template-columns: 32px 1fr 32px 32px; padding: 0 10px;">
                                                            <a title="Play" id="history-player-${item.id}-action" class="history-player-action" style="display: inline-block; height: 32px; width: 32px; padding: 4px; cursor: pointer; margin-top: -4px;" onclick="playerAction('${item.id}');">
                                                                <img src="/assets/play.svg" style="display: inline-block; filter: invert(1); height: 32px; width: 32px; padding: 4px; border-radius: 999px;" alt="Play" id="history-player-${item.id}-play">
                                                                <img src="/assets/stop.svg" style="display: none; filter: invert(1); height: 32px; width: 32px; padding: 4px; border-radius: 999px;" alt="Stop" id="history-player-${item.id}-stop">
                                                            </a>
                                                            <div style="display: flex; align-items: center; margin: 0 10px;">
                                                                <div style="width: 100%; background-color: rgba(255, 255, 255, .1); height: 8px; border-radius: 999px;">
                                                                    <div id="history-player-${item.id}-bar" style="height: 8px; border-radius: 999px; width: 0; background-color: rgba(255, 255, 255, .25);"></div>
                                                                </div>
                                                            </div>
                                                            <a title="Download" class="history-player-action" style="display: inline-block; height: 32px; width: 32px; padding: 4px; cursor: pointer; margin-top: -4px;" onclick="playerDownload('${item.id}', '${item.filename}');">
                                                                <img src="/assets/download.svg" style="display: inline-block; filter: invert(1); height: 32px; width: 32px; padding: 4px; border-radius: 999px;" alt="Download">
                                                            </a>
                                                            <a title="Remove from history" class="history-player-action" style="display: inline-block; height: 32px; width: 32px; padding: 4px; cursor: pointer; margin-top: -4px;" onclick="playerDelete('${item.id}', '${item.filename}');">
                                                                <img src="/assets/delete.svg" style="display: inline-block; filter: invert(1); height: 32px; width: 32px; padding: 4px; border-radius: 999px;" alt="Remove from history">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="history-loading" style="display: none; height: 32px;">
                                                        <img src="/assets/favicon-mono.svg" style="height: 32px;animation-duration: 1s;width: 32px;animation-name: pulse;animation-timing-function: linear;animation-iteration-count: infinite;animation-direction: alternate;" alt="" class="load-icon">
                                                        <span class="history-loading-text" style="margin-left: 10px;vertical-align: middle;">...</span>
                                                    </div>
                                                </div>
                                            `);

                                        window.playIntervals = {};

                                        let id = item.id;
                                        let audio = document.getElementById("history-player-" + id);
                                        let btn = document.getElementById("history-player-" + id + "-action");
                                        let btnPlay = document.getElementById("history-player-" + id + "-play");
                                        let btnStop = document.getElementById("history-player-" + id + "-stop");
                                        let bar = document.getElementById("history-player-" + id + "-bar");

                                        document.getElementById("history-player-" + id).ondurationchange = () => {
                                            bar.style.width = "0%";
                                        }

                                        document.getElementById("history-player-" + id).onplay = () => {
                                            window.playIntervals[id] = setInterval(() => {
                                                bar.style.width = ((audio.currentTime / audio.duration) * 100) + "%";
                                            });
                                        }

                                        document.getElementById("history-player-" + id).onended = () => {
                                            clearInterval(window.playIntervals[id]);

                                            bar.style.width = "0%";
                                            audio.currentTime = 0;
                                            audio.muted = false;
                                            audio.volume = 1;

                                            btn.title = "Play";
                                            btnPlay.style.display = "inline-block";
                                            btnStop.style.display = "none";
                                        }
                                    }

                                    if (item.explicit) {
                                        if (document.querySelector("#history-" + item.id + " .history-prompt")) document.querySelector("#history-" + item.id + " .history-prompt").classList.add("explicit");
                                    } else {
                                        if (document.querySelector("#history-" + item.id + " .history-prompt")) document.querySelector("#history-" + item.id + " .history-prompt").classList.remove("explicit");
                                    }

                                    if (item.processed) {
                                        document.querySelector("#history-" + item.id + " > .history-player").style.display = "";
                                        if (document.querySelector("#history-" + item.id + " > .history-loading")) document.querySelector("#history-" + item.id + " > .history-loading").style.display = "none";

                                        if (document.querySelector("#history-" + item.id + " > .history-player > audio").src.trim() === "") {
                                            document.querySelector("#history-" + item.id + " > .history-player > audio").src = "https://cdn.equestria.dev/sunnystarbot/content/" + item.id + "/audio.wav";
                                        }
                                    } else {
                                        document.querySelector("#history-" + item.id + " > .history-player").style.display = "none";
                                        if (document.querySelector("#history-" + item.id + " > .history-loading")) {
                                            document.querySelector("#history-" + item.id + " > .history-loading").style.display = "";

                                            if (item.crashed) {
                                                document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "That didn't quite work... We can try that again!";
                                            } else if (item.queued) {
                                                document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "We're out of glitter! Be right back!";
                                            } else {
                                                let n = parseInt(item.id.substring(0, 1), 16);

                                                switch (n) {
                                                    default:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Finding your sparkle...";
                                                        break;

                                                    case 1:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Making your mark...";
                                                        break;

                                                    case 2:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Putting hooves together...";
                                                        break;

                                                    case 3:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Giving you a twist...";
                                                        break;

                                                    case 4:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Letting your mane down...";
                                                        break;

                                                    case 5:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Busting a hoof...";
                                                        break;

                                                    case 6:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Showing you pony moves...";
                                                        break;

                                                    case 7:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Coming together...";
                                                        break;

                                                    case 8:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Lifting up your hooves...";
                                                        break;

                                                    case 9:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Galloping across Equestria...";
                                                        break;

                                                    case 10:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Spreading love from you to me...";
                                                        break;

                                                    case 11:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Working together...";
                                                        break;

                                                    case 12:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Taking a look a little closer...";
                                                        break;

                                                    case 13:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Making you sparkle again...";
                                                        break;

                                                    case 14:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Reigniting your spark...";
                                                        break;

                                                    case 15:
                                                        document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Not forgetting about your friends...";
                                                        break;
                                                }
                                            }
                                        }
                                    }

                                    document.querySelector("#history-" + item.id + " > div > .history-time").innerText = timeAgo(item.time) + " · " + item.version;
                                } else {
                                    if (document.getElementById("history-" + item.id)) {
                                        document.getElementById("history-" + item.id).outerHTML = "";
                                    }
                                }

                                index++;
                            }
                        } else {
                            document.getElementById("list").style.display = "none";
                            document.getElementById("list-message").style.display = "";

                            document.getElementById("list-message").innerText = "You have not generated anything yet!";
                        }
                    }
                </script>
            </form>

            <div id="mobile-separator">
                <hr>
            </div>
        </div>

        <div id="pane-left" style="order: 1;">
            <h2 style="margin-bottom: 30px;">Your audio generations</h2>

            <div id="list" class="list-group"></div>

            <div id="list-message" style="font-style: italic;">Loading previous generations...</div>
        </div>
    </div>
</main>

<div class="container" id="bottom">
    <hr>

    <div class="small text-muted">
        Protected by reCAPTCHA (see Google's <a href="https://policies.google.com/terms" target="_blank">Terms of service</a> or <a href="https://policies.google.com/privacy" target="_blank">Privacy policy</a>)<br>
        Made with ❤ by ponies in Equestria. My Little Pony is ™ and © Hasbro, All rights reserved. Artwork is the property of Hasbro.<br>
        © <?= date('Y') ?> <a href="https://equestria.dev" target="_blank">Equestria.dev Developers</a> · <a href="https://equestria.dev" target="_blank" data-bs-toggle="modal" data-bs-target="#terms">Terms of use</a> · Not created or endorsed by Hasbro.
    </div>

    <br><br>
</div>

<div class="modal fade" id="terms">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Terms of use</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <b>In short:</b>
                <ul>
                    <li>Follow the <a style="color: white;" href="https://equestria.dev/legal/terms/" target="_blank">Equestria.dev Terms of Service</a></li>
                    <li>Credit "Sunny Starbot" and don't mix it with other text-to-speech engines</li>
                    <li>All content is kept and viewed by the administrators</li>
                    <li>Content must be family-friendly</li>
                    <li>No religion or politics</li>
                    <li>No impersonation or claiming to be Hasbro</li>
                    <li>No bots or excessive use</li>
                </ul>

                <hr>
                <p>These terms of use govern your use of the Sunny Starbot project and any access to the Sunny Starbot AI model you might have access to. It is considered that you have read and agreed to the following as soon as you start using Sunny Starbot. These terms of use complement the <a style="color: white;" href="https://equestria.dev/legal/terms/" target="_blank">Equestria.dev Online Services Terms of Service</a> for the case of Sunny Starbot only.</p>
                <p>Users are granted exclusive limited access to Sunny Starbot that may be revoked at any time at the administrators' sole discretion, regardless of a breach in the following terms of use or not. To ensure respect of these terms, Equestria.dev will store and manually review all requests made to the service, including blocked requests, and even if they have been removed from your history.</p>
                <p>Content generated through the use of Sunny Starbot must remain family-friendly and not cause harm to anyone, and, in case such content is shared, must credit "Sunny Starbot" or "Equestria.dev" and not be mixed with other text-to-speech engines to avoid confusion. This means violent, explicit, vulgar, religious, political, or other harmful content is not allowed. Impersonation, or claiming to be an official Hasbro content or entity, is not allowed.</p>
                <div>Sunny Starbot runs on limited resources, therefore, users must use the service in a fair and non-abusive way. With that said, any use of automated software (or "bots") to generate content automatically is not allowed. Sunny Starbot makes use of Google's reCAPTCHA technology as well as rate limits to accomplish automatic blocking of bots. Users who make an excessive number of requests, even without using automated software, may also be acted upon.</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cf">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Unable to process request</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-danger">
                    <p><b>Your request contains inappropriate content.</b></p>
                    <p>Sunny Starbot aims to provide results that are appropriate for all ages. Any potential content that exceeds the show's rating is not allowed. For more information, please read the terms of use.</p>
                    You may now modify your request to remove the corresponding content.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="robot">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Unable to process request</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-danger">
                    <p><b>You might be a robot.</b></p>
                    <p>Our systems have detected you might be using an automated program to massively send requests to Sunny Starbot.</p>
                    If you think this is not correct, please try again.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.modal = new bootstrap.Modal(document.getElementById("cf"));
    window.lastList = null;
    window.lastPossible = null;

    document.getElementById("cf").addEventListener('hidden.bs.modal', (event) => {
        document.getElementById("input").focus();
    });

    window.modal2 = new bootstrap.Modal(document.getElementById("robot"));

    document.getElementById("robot").addEventListener('hidden.bs.modal', (event) => {
        document.getElementById("input").focus();
    });

    setInterval(() => {
        refreshList();
        checkPossible();
    }, 1000);

    function configureRefresh() {
        window.stream = new EventSource('/app/stream.php');

        stream.onerror = (e) => {
            console.log(e);

            setTimeout(() => {
                configureRefresh();
            }, 1000);
        }

        stream.onmessage = (e) => {
            try {
                let data = JSON.parse(atob(e.data));

                if (e.lastEventId === "list") {
                    window.lastList = data;
                    window.listData = data;
                    refreshList();
                } else if (e.lastEventId === "possible") {
                    window.lastPossible = data;
                    window.possibleData = data;
                    checkPossible();
                }
            } catch (err) {
                console.error(err);
            }
        }

        fetch("/app/list.php").then((res) => {
            res.json().then((data) => {
                window.listData = data;
                refreshList();
            });
        });

        fetch("/app/possible.php").then((res) => {
            res.json().then((data) => {
                window.possibleData = data;
                checkPossible();
            });
        });
    }

    configureRefresh();
</script>

</body>
</html>

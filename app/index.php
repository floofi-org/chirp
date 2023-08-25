<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sunny Starbot</title>
    <script src="https://www.google.com/recaptcha/api.js?render=<?= json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true)["recaptcha"]["site"] ?>"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
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

        * {
            font-family: Jost, -apple-system, system-ui, "Segoe UI", "Noto Sans", sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: MLPFindYourSparkle, Jost, -apple-system, system-ui, "Segoe UI", "Noto Sans", sans-serif;
        }

        @media (max-width: 700px) {
            #mobile-separator { display: block; }
            #panes { grid-template-columns: 1fr !important; grid-gap: 0 !important; }
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
    </style>
    <link rel="shortcut icon" href="/favicon.svg" type="image/svg+xml">
</head>
<body style="background-image: url('/bg.png'); background-size: cover; background-position: center; background-attachment: fixed;">
<br><br>

<main class="container" style="background-color: #9c1d96; color: white; border-radius: 30px; padding: 30px;">
    <img src="/banner.png" alt="Sunny Starbot" style="width: 100%; max-width: 768px; display: block; margin-left: auto; margin-right: auto; margin-bottom: 30px;">

    <div style="display: grid; grid-template-columns: 1fr 1fr; grid-gap: 30px;" id="panes">
        <div id="pane-left">
            <h2 style="margin-bottom: 30px;">Generate something</h2>

            <form onsubmit="sendRequest(); return false;">
                <textarea class="form-control" rows="3" style="resize: none;" id="input" autofocus placeholder="Enter text here." maxlength="160" disabled></textarea>

                <div style="margin-top: 1rem; display: grid; grid-template-columns: 1fr max-content; grid-gap: 10px;">
                    <div style="align-items: center; display: flex; align-items: center; justify-content: center;" class="text-muted">
                        <div>
                            Your input will be read by real people, never enter personal or confidential information.
                        </div>
                    </div>
                    <div>
                        <button class="btn btn-primary" id="submit-btn" disabled>Generate</button>
                    </div>
                </div>

                <script>
                    window.processing = false;

                    function checkPossible() {
                        fetch("/app/possible.php").then((res) => {
                            res.json().then((possible) => {
                                if (!processing && possible && document.getElementById("submit-btn").disabled) {
                                    document.getElementById("submit-btn").disabled = false;
                                    document.getElementById("input").disabled = false;
                                    if (!modal || !modal._isShown) document.getElementById("input").focus();
                                } else if ((!possible || processing) && !document.getElementById("submit-btn").disabled) {
                                    document.getElementById("submit-btn").disabled = true;
                                    document.getElementById("input").disabled = true;
                                }
                            });
                        });
                    }

                    setInterval(checkPossible, 1000);
                    checkPossible();

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
                                                refreshList();
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

                    function refreshList() {
                        fetch("/app/list.php").then((res) => {
                            res.json().then((data) => {
                                if (data.length > 0) {
                                    document.getElementById("list-message").style.display = "none";
                                    document.getElementById("list").style.display = "";

                                    for (let item of data) {
                                        if (!document.getElementById("history-" + item.id)) {
                                            document.getElementById("list").insertAdjacentHTML("afterbegin", `
                                        <div id="history-${item.id}" class="list-group-item">
                                            <div style="display: grid; grid-template-columns: 1fr max-content; margin-bottom: 10px;">
                                                <div class="history-prompt"><b>${item.input.replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;")}</b></div>
                                                <div class="history-time text-muted">${timeAgo(item.time)}</div>
                                            </div>
                                            <div class="history-player" style="display: none; height: 32px;">
                                                <audio controls controlslist="nofullscreen noremoteplayback noplaybackrate" disableremoteplayback x-webkit-airplay="deny" style="height: 32px;">
                                            </div>
                                            <div class="history-loading" style="display: none; height: 32px;">
                                                <img src="/favicon-mono.svg" style="height: 32px;animation-duration: 1s;width: 32px;animation-name: pulse;animation-timing-function: linear;animation-iteration-count: infinite;animation-direction: alternate;" alt="" class="load-icon">
                                                <span class="history-loading-text" style="margin-left: 10px;vertical-align: middle;">...</span>
                                            </div>
                                        </div>
                                    `);
                                        }

                                        if (item.processed) {
                                            document.querySelector("#history-" + item.id + " > .history-player").style.display = "";
                                            document.querySelector("#history-" + item.id + " > .history-loading").style.display = "none";

                                            if (document.querySelector("#history-" + item.id + " > .history-player > audio").src.trim() === "") {
                                                document.querySelector("#history-" + item.id + " > .history-player > audio").src = "/static/" + item.id + "/audio.wav";
                                            }
                                        } else {
                                            document.querySelector("#history-" + item.id + " > .history-player").style.display = "none";
                                            document.querySelector("#history-" + item.id + " > .history-loading").style.display = "";

                                            if (item.crashed) {
                                                document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "An error occurred, please try again later.";
                                            } else if (item.queued) {
                                                document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Our servers are currently at peak, please wait...";
                                            } else {
                                                document.querySelector("#history-" + item.id + " > .history-loading > .history-loading-text").innerText = "Generating audio, please wait...";
                                            }
                                        }

                                        document.querySelector("#history-" + item.id + " > div > .history-time").innerText = timeAgo(item.time);
                                    }
                                } else {
                                    document.getElementById("list").style.display = "none";
                                    document.getElementById("list-message").style.display = "";

                                    document.getElementById("list-message").innerText = "You have not generated anything yet!";
                                }
                            });
                        })
                    }
                </script>
            </form>

            <div id="mobile-separator">
                <hr>
            </div>
        </div>

        <div id="pane-right">
            <h2 style="margin-bottom: 30px;">Your audio generations</h2>

            <div id="list" class="list-group"></div>

            <div id="list-message" style="font-style: italic;">Loading previous generations...</div>

            <script>
                setInterval(refreshList, 1000);
                refreshList();
            </script>
        </div>
    </div>

    <br><br>
</main>

<div class="container" id="bottom">
    <hr>

    <div class="small text-muted">
        Protected by reCAPTCHA (see Google's <a href="https://policies.google.com/terms" target="_blank">Terms of service</a> or <a href="https://policies.google.com/privacy" target="_blank">Privacy policy</a>)<br>
        Made with ❤ by ponies in Equestria.
    </div>

    <br><br>
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
                    <p>Sunny Starbot aims to provide results that are appropriate for all ages. Any potential content that exceeds the show's rating is not allowed. For more information, please read the terms of service.</p>
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

    document.getElementById("cf").addEventListener('hidden.bs.modal', (event) => {
        document.getElementById("input").focus();
    });

    window.modal2 = new bootstrap.Modal(document.getElementById("robot"));

    document.getElementById("robot").addEventListener('hidden.bs.modal', (event) => {
        document.getElementById("input").focus();
    });
</script>

</body>
</html>
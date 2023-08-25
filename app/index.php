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
    <style>.grecaptcha-badge { visibility: hidden; }</style>
</head>
<body>
<div class="container">
    <br><br>
    <h1>Sunny Starbot</h1>

    <form onsubmit="sendRequest(); return false;">
        <textarea class="form-control" rows="3" style="resize: none;" id="input" autofocus placeholder="Enter text here." onchange="refreshCharacterLimit();" onkeyup="refreshCharacterLimit();" maxlength="160" disabled></textarea>

        <div style="margin-top: 1rem; display: grid; grid-template-columns: 1fr max-content max-content;">
            <div style="display: flex; align-items: center;" class="text-muted" id="characters">
                0/160
            </div>
            <div style="display: flex; align-items: center; margin-right: 10px;" class="text-muted" id="characters">
                <span id="confirm-pc">⌃↵</span><span id="confirm-mac">⌘↵</span>
                <script>
                    if (navigator.userAgent.includes("Macintosh")) {
                        document.getElementById("confirm-pc").style.display = "none";
                    } else {
                        document.getElementById("confirm-mac").style.display = "none";
                    }
                </script>
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
                refreshCharacterLimit();

                if (event.code === "Enter" && (event.metaKey || event.ctrlKey)) {
                    sendRequest();
                }
            }

            function refreshCharacterLimit() {
                document.getElementById("characters").innerText = document.getElementById("input").value.length + "/160";
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
                                            <div class="history-player" style="display: none;">
                                                <audio controls controlslist="nofullscreen noremoteplayback noplaybackrate" disableremoteplayback x-webkit-airplay="deny">
                                            </div>
                                            <div class="history-loading" style="display: none;">
                                                Generating...
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

    <hr>

    <div id="list" class="list-group"></div>

    <div id="list-message" style="font-style: italic;">Loading previous generations...</div>

    <script>
        setInterval(refreshList, 1000);
        refreshList();
    </script>

    <hr>

    <div class="small text-muted">
        Protected by reCAPTCHA (<a href="https://policies.google.com/terms" target="_blank">Terms of service</a>/<a href="https://policies.google.com/privacy" target="_blank">Privacy policy</a>)<br>
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
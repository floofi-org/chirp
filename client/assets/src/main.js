(async () => {
    let code = (await (await fetch("https://voice-api.floo.fi/api/v2/status", {
        credentials: "include",
        headers: {
            "Authorization": localStorage.getItem('token') ? "PrivateToken " + localStorage.getItem('token') : ''
        }
    })).json()).error?.code;

    if (code === 401) {
        location.href = "/";
        return;
    } else if (code === 403) {
        location.href = "/banned";
        return;
    }

    window._fetch = fetch;
    window.fetch = async (input, init) => {
        if (!init) init = {};
        init["credentials"] = "include";
        if (!init["headers"]) init["headers"] = {};
        init["headers"]["Authorization"] = localStorage.getItem('token') ? "PrivateToken " + localStorage.getItem('token') : '';

        return await _fetch(input, init);
    }

    window.models = (await (await fetch("https://voice-api.floo.fi/api/v2/models")).json()).output;
    window.processing = false;

    document.getElementById("model").innerHTML = window.models.map(i => `
                <option value="${i.id}">${i.name}</option>
            `).join("");
    document.getElementById("model-details").innerHTML = window.models.map(i => `
                <div id="model-${i.id}" class="model text-muted small" style="text-align: center; display: none;">v${i.version} • ${i.source}</div>
            `).join("");

    refreshModel();

    window.modelIdToName = (id) => {
        return window.models.find(i => i.id === id)?.name ?? "(unknown)";
    }

    window.checkPossible = () => {
        let possible = window.possibleData;

        if (!processing && possible && document.getElementById("submit-btn").classList.contains("disabled")) {
            document.getElementById("submit-btn").classList.remove("disabled");
            document.getElementById("input").disabled = false;
            document.getElementById("input").focus();
        } else if ((!possible || processing) && !document.getElementById("submit-btn").classList.contains("disabled")) {
            document.getElementById("submit-btn").classList.add("disabled");
            document.getElementById("input").disabled = true;
        }
    }

    document.getElementById("input").onkeydown = (event) => {
        if (event.code === "Enter" && (event.metaKey || event.ctrlKey)) {
            sendRequest();
        }
    }

    window.sendRequest = () => {
        if (document.getElementById("input").value.trim() !== "") {
            window.processing = true;
            document.getElementById("submit-btn").classList.add("disabled");
            document.getElementById("input").disabled = true;

            fetch("https://voice-api.floo.fi/api/v2/generate", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    input: document.getElementById("input").value,
                    model: document.getElementById("model").value
                })
            }).then((res) => {
                res.json().then((data) => {
                    window.processing = false;

                    if (!data['error']) {
                        document.getElementById("input").value = "";

                        fetch("https://voice-api.floo.fi/api/v2/history?amount=30").then((res) => {
                            res.json().then((data) => {
                                window.listData = data['output']['history'];
                                refreshList();
                            });
                        });

                        fetch("https://voice-api.floo.fi/api/v2/available").then((res) => {
                            res.json().then((data) => {
                                window.possibleData = data['output']['available'];
                                checkPossible();
                            });
                        });
                    } else {
                        document.getElementById("submit-btn").classList.remove("disabled");
                        document.getElementById("input").disabled = false;
                        location.href = "/blocked";
                    }
                });
            });
        }
    }

    window.timeAgo = (time) => {
        if (!isNaN(parseInt(time))) {
            time = new Date(time).getTime();
        }

        let periods = ["second", "minute", "hour", "day", "week", "month", "year", "age"];

        let lengths = ["60", "60", "24", "7", "4.35", "12", "100"];

        let now = new Date().getTime();

        let difference = Math.round((now - time) / 1000);
        let tense;
        let period;

        if (difference <= 60) {
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

    window.playerAction = (id) => {
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

    window.playerDelete = (id) => {
        document.getElementById("history-" + id).outerHTML = "";

        fetch("https://voice-api.floo.fi/api/v2/history/" + id, {
            method: "DELETE"
        }).then(() => {
            fetch("https://voice-api.floo.fi/api/v2/history?amount=30").then((res) => {
                res.json().then((data) => {
                    window.listData = data['output']['history'];
                    window.lastList = data['output']['history'];
                    refreshList();
                });
            });
        });
    }

    window.playerCopy = (id) => {
        navigator.clipboard.writeText(document.getElementById("history-" + id).getElementsByClassName("history-prompt")[0].innerText);
    }

    window.playerDownload = (id, name) => {
        let element = document.createElement("a");
        element.setAttribute("download", name);
        element.setAttribute("href", "https://cdn.equestria.dev/sunnystarbot/content/" + id + "/audio.wav");
        element.click();
    }

    window.refreshList = () => {
        let data = window.listData.filter(i => {
            return i.status !== "removed";
        }).reverse();

        if (data.length > 0) {
            document.getElementById("list-message").style.display = "none";
            document.getElementById("list").style.display = "";

            for (let id of Array.from(document.getElementById("list").children).map(i => i.id.split("-")[1])) {
                if (!data.map(i => i.id).includes(id)) {
                    document.getElementById("history-" + id).outerHTML = "";
                }
            }

            for (let item of data) {
                if (!document.getElementById("history-" + item.id)) {
                    document.getElementById("list").insertAdjacentHTML("afterbegin", `
                                <div id="history-${item.id}" class="fella-list-item fella-list-item-padded history-item" style="display: grid; grid-template-columns: 2.5fr 1fr; grid-gap: 30px; height: 132px;">
                                    <div style="display: flex; align-items: center; width: 100%;">
                                        <div style="width: 100%;">
                                            <div style="margin: 0 24px 16px 24px;">
                                                <div class="history-prompt" style="white-space: nowrap;overflow: hidden !important;text-overflow: ellipsis;" title="${item.input.replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;").replaceAll('"', "&quot;")}"><b>${item.input.replaceAll("&", "&amp;").replaceAll("<", "&lt;").replaceAll(">", "&gt;")}</b></div>
                                                <div class="history-time text-muted small">${timeAgo(item.time)} · ${modelIdToName(item.model)} (v${item.version})</div>
                                            </div>
                                            <div class="history-player" style="display: none; height: 32px; border: 1px solid var(--fella-border); border-radius: 999px;">
                                                <audio id="history-player-${item.id}"></audio>
                                                <div style="height: 32px; display: grid; grid-template-columns: 32px 1fr 32px 32px 32px; padding: 0 10px;">
                                                    <a title="Play" id="history-player-${item.id}-action" class="history-player-action" style="display: inline-block; height: 32px; width: 32px; padding: 4px; cursor: pointer; margin-top: -4px;" onclick="playerAction('${item.id}');">
                                                        <svg id="history-player-${item.id}-play" style="display: inline-block; height: 24px; width: 24px; padding: 4px; border-radius: 999px;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="6 3 20 12 6 21 6 3"/></svg>
                                                        <svg id="history-player-${item.id}-stop" style="display: none; height: 24px; width: 24px; padding: 4px; border-radius: 999px;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="14" y="4" width="4" height="16" rx="1"/><rect x="6" y="4" width="4" height="16" rx="1"/></svg>
                                                    </a>
                                                    <div style="display: flex; align-items: center; margin: 0 10px; height: 32px;">
                                                        <div style="width: 100%; background-color: rgba(255, 255, 255, .1); height: 8px; border-radius: 999px;">
                                                            <div id="history-player-${item.id}-bar" style="height: 8px; border-radius: 999px; width: 0; background-color: rgba(255, 255, 255, .25);"></div>
                                                        </div>
                                                    </div>
                                                    <a title="Copy text to clipboard" class="history-player-action" style="display: inline-block; height: 32px; width: 32px; padding: 4px; cursor: pointer; margin-top: -4px;" onclick="playerCopy('${item.id}', '${item.filename}');">
                                                        <svg  style="display: inline-block; height: 24px; width: 24px; padding: 4px; border-radius: 999px;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/></svg>
                                                    </a>
                                                    <a title="Download" class="history-player-action" style="display: inline-block; height: 32px; width: 32px; padding: 4px; cursor: pointer; margin-top: -4px;" onclick="playerDownload('${item.id}', '${item.filename}');">
                                                        <svg style="display: inline-block; height: 24px; width: 24px; padding: 4px; border-radius: 999px;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                                    </a>
                                                    <a title="Remove from history" class="history-player-action" style="display: inline-block; height: 32px; width: 32px; padding: 4px; cursor: pointer; margin-top: -4px;" onclick="playerDelete('${item.id}', '${item.filename}');">
                                                        <svg style="display: inline-block; height: 24px; width: 24px; padding: 4px; border-radius: 999px;" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="history-loading" style="display: flex; align-items: center; margin: 0 24px;">
                                                <svg class="fella-loader" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" style="shape-rendering: auto; display: block; background: transparent;">
                                                    <g><g transform="rotate(0 50 50)">
                                                        <rect fill="#8f8f8f" height="16" width="5" ry="2.56" rx="2.5" y="22" x="47.5">
                                                            <animate repeatCount="indefinite" begin="-0.9166666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                        </rect>
                                                    </g><g transform="rotate(30 50 50)">
                                                        <rect fill="#8f8f8f" height="16" width="5" ry="2.56" rx="2.5" y="22" x="47.5">
                                                            <animate repeatCount="indefinite" begin="-0.8333333333333334s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                        </rect>
                                                    </g><g transform="rotate(60 50 50)">
                                                        <rect fill="#8f8f8f" height="16" width="5" ry="2.56" rx="2.5" y="22" x="47.5">
                                                            <animate repeatCount="indefinite" begin="-0.75s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                        </rect>
                                                    </g><g transform="rotate(90 50 50)">
                                                        <rect fill="#8f8f8f" height="16" width="5" ry="2.56" rx="2.5" y="22" x="47.5">
                                                            <animate repeatCount="indefinite" begin="-0.6666666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                        </rect>
                                                    </g><g transform="rotate(120 50 50)">
                                                        <rect fill="#8f8f8f" height="16" width="5" ry="2.56" rx="2.5" y="22" x="47.5">
                                                            <animate repeatCount="indefinite" begin="-0.5833333333333334s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                        </rect>
                                                    </g><g transform="rotate(150 50 50)">
                                                        <rect fill="#8f8f8f" height="16" width="5" ry="2.56" rx="2.5" y="22" x="47.5">
                                                            <animate repeatCount="indefinite" begin="-0.5s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                        </rect>
                                                    </g><g transform="rotate(180 50 50)">
                                                        <rect fill="#8f8f8f" height="16" width="5" ry="2.56" rx="2.5" y="22" x="47.5">
                                                            <animate repeatCount="indefinite" begin="-0.4166666666666667s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                        </rect>
                                                    </g><g transform="rotate(210 50 50)">
                                                        <rect fill="#8f8f8f" height="16" width="5" ry="2.56" rx="2.5" y="22" x="47.5">
                                                            <animate repeatCount="indefinite" begin="-0.3333333333333333s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                        </rect>
                                                    </g><g transform="rotate(240 50 50)">
                                                        <rect fill="#8f8f8f" height="16" width="5" ry="2.56" rx="2.5" y="22" x="47.5">
                                                            <animate repeatCount="indefinite" begin="-0.25s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                        </rect>
                                                    </g><g transform="rotate(270 50 50)">
                                                        <rect fill="#8f8f8f" height="16" width="5" ry="2.56" rx="2.5" y="22" x="47.5">
                                                            <animate repeatCount="indefinite" begin="-0.16666666666666666s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                        </rect>
                                                    </g><g transform="rotate(300 50 50)">
                                                        <rect fill="#8f8f8f" height="16" width="5" ry="2.56" rx="2.5" y="22" x="47.5">
                                                            <animate repeatCount="indefinite" begin="-0.08333333333333333s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                        </rect>
                                                    </g><g transform="rotate(330 50 50)">
                                                        <rect fill="#8f8f8f" height="16" width="5" ry="2.56" rx="2.5" y="22" x="47.5">
                                                            <animate repeatCount="indefinite" begin="0s" dur="1s" keyTimes="0;1" values="1;0" attributeName="opacity"></animate>
                                                        </rect>
                                                    </g><g></g></g>
                                                </svg>
                                                <span class="history-loading-text fella-footnotes" style="margin-top: 0; margin-left: 10px;vertical-align: middle;">...</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="display: none; height: 114px; align-items: center;" class="history-spectrogram">
                                        <img alt="Spectogram" class="history-spectrogram-img" style="width: 100%;">
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

                if (item['explicit']) {
                    if (document.querySelector("#history-" + item.id + " .history-prompt")) document.querySelector("#history-" + item.id + " .history-prompt").classList.add("explicit");
                } else {
                    if (document.querySelector("#history-" + item.id + " .history-prompt")) document.querySelector("#history-" + item.id + " .history-prompt").classList.remove("explicit");
                }

                if (item.status === "processed") {
                    document.querySelector("#history-" + item.id + " .history-player").style.display = "";
                    document.querySelector("#history-" + item.id + " .history-spectrogram").style.display = "flex";
                    document.querySelector("#history-" + item.id + " .history-spectrogram-img").src = "https://cdn.equestria.dev/sunnystarbot/content/" + item.id + "/figure.png";
                    if (document.querySelector("#history-" + item.id + " .history-loading")) document.querySelector("#history-" + item.id + " .history-loading").style.display = "none";

                    if (document.querySelector("#history-" + item.id + " .history-player audio").src.trim() === "") {
                        document.querySelector("#history-" + item.id + " .history-player audio").src = "https://cdn.equestria.dev/sunnystarbot/content/" + item.id + "/audio.wav";
                    }
                } else {
                    document.querySelector("#history-" + item.id + " .history-player").style.display = "none";
                    document.querySelector("#history-" + item.id + " .history-spectrogram").style.display = "none";
                    if (document.querySelector("#history-" + item.id + " .history-loading")) {
                        document.querySelector("#history-" + item.id + " .history-loading").style.display = "flex";

                        if (item.status === "crashed") {
                            document.querySelector("#history-" + item.id + " .history-loading .history-loading-text").innerText = "Failed to generate.";
                        } else if (item.status === "queued") {
                            document.querySelector("#history-" + item.id + " .history-loading .history-loading-text").innerText = "Waiting for an available server...";
                        } else {
                            document.querySelector("#history-" + item.id + " .history-loading .history-loading-text").innerText = "Generating...";
                        }
                    }
                }

                document.querySelector("#history-" + item.id + " .history-time").innerHTML = timeAgo(item.time) + "<span class='fella-footnotes' style='margin-top: 0;'> · " + modelIdToName(item.model) + " (v" + item.version + ")</span>";
            }
        } else {
            document.getElementById("list").style.display = "none";
            document.getElementById("list-message").style.display = "";

            document.getElementById("list-message").innerText = "You have not generated anything yet!";
        }
    }

    document.getElementById("footer-year").innerText = new Date().getUTCFullYear().toString();

    window.lastList = null;
    window.lastPossible = null;

    window.configureRefresh = () => {
        function refresh() {
            fetch("https://voice-api.floo.fi/api/v2/history?amount=30").then((res) => {
                res.json().then((data) => {
                    window.listData = data['output']['history'];
                    refreshList();
                });
            });

            fetch("https://voice-api.floo.fi/api/v2/available").then((res) => {
                res.json().then((data) => {
                    window.possibleData = data['output']['available'];
                    checkPossible();
                });
            });
        }

        refresh();
        setInterval(refresh, 5000);
    }

    configureRefresh();
    await prepareNavbar();

    document.getElementById("loader").style.display = "none";
    document.getElementById("app").style.display = "";
})();

function refreshModel() {
    let id = document.getElementById("model").value;
    Array.from(document.getElementsByClassName("model")).map(i => i.style.display = "none");
    document.getElementById("model-" + id).style.display = "";
    document.getElementById("preview").style.backgroundImage = 'url("/assets/models/' + id + '.webp")';
}

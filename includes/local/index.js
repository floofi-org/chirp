#!/usr/bin/env node
const fs = require('fs');
const cp = require('child_process');
process.chdir(__dirname);
const readline = require('node:readline');
const { stdin: input, stdout: output } = require('node:process');

try { cp.execSync("pkill -9 python3"); } catch (e) {}
try { cp.execSync("pkill -9 Python"); } catch (e) {}

console.log('Sunny Starbot local frontend');
console.log("(c) Equestria.dev Developers");
console.log("");

/*console.log("Available versions:");
let list = fs.readdirSync(__dirname + "/../runtime/versions").sort((a, b) => {
    return parseInt(b.split(".")[0]) - parseInt(a.split(".")[0]);
});
list.map((i, j) => {
    console.log("- " + i);
});

function select() {
    const rl = readline.createInterface({ input, output });

    rl.on('SIGINT', () => {
        if (typeof p !== "undefined") p.kill("SIGKILL");
        try { cp.execSync("pkill -9 python3"); } catch (e) {}
        try { cp.execSync("pkill -9 Python"); } catch (e) {}
        process.exit(0);
    });

    rl.question('\nEnter version number: ', (answer) => {
        rl.close();

        let version = null;
        let matches = 0;

        for (let v of list) {
            if (v.startsWith(answer)) {
                matches++;
                version = v;
            }
        }

        if (matches === 0) {
            console.log("Could not find the wanted version.");
            select();
            return;
        } else if (matches > 1) {
            console.log("More than one version is matching your query.");
            select();
            return;
        }

        start(version);
    });
}

select();*/

start("../MLPTTS");

function start(version) {
    /*console.log("Model version: " + version);
    console.log("");*/

    let p = cp.exec("source ./venv/bin/activate; python3 main.py versions/" + version, { stdio: "pipe", cwd: __dirname + "/../runtime" });
    p.on('exit', (code) => {
        if (code !== 0) {
            throw new Error("Engine quit unexpectedly with code " + code);
        }
    });
    p.ref();

    global.lastTime = 0;
    listen();

    for (let i of ["SIGINT", "SIGTERM", "SIGQUIT", "quit", "beforeQuit"]) {
        process.on(i, () => {
            p.kill("SIGKILL");
            try { cp.execSync("pkill -9 python3"); } catch (e) {}
            try { cp.execSync("pkill -9 Python"); } catch (e) {}
            process.exit(0);
        });
    }

    function listen() {
        const rl = readline.createInterface({ input, output });

        rl.on('SIGINT', () => {
            p.kill("SIGKILL");
            try { cp.execSync("pkill -9 python3"); } catch (e) {}
            try { cp.execSync("pkill -9 Python"); } catch (e) {}
            process.exit(0);
        });

        rl.question(global.lastTime + '> ', (answer) => {
            answer = answer.trim();

            if (answer === "") {
                p.kill("SIGKILL");
                try { cp.execSync("pkill -9 python3"); } catch (e) {}
                try { cp.execSync("pkill -9 Python"); } catch (e) {}
                process.exit(0);
                return;
            }

            rl.close();

            let start;
            let id = start = new Date().getTime();

            fs.mkdirSync("../runtime/outputs/" + id + "");
            fs.writeFileSync("../runtime/outputs/" + id + "/input.txt", answer);
            process.stdout.write("Waiting for the engine to start...");

            let waitInterval = setInterval(() => {
                if (fs.existsSync("../runtime/outputs/" + id + "/process.txt")) {
                    if (fs.existsSync("../runtime/outputs/" + id + "/complete.txt")) {
                        global.lastTime = new Date().getTime() - start;
                        clearInterval(waitInterval);
                        process.stdout.clearLine(null);
                        process.stdout.cursorTo(0);

                        try {
                            cp.execSync("afplay audio.wav", { cwd: __dirname + "/../runtime/outputs/" + id, stdio: "ignore" });
                        } catch (e) {
                            cp.execSync("aplay audio.wav", { cwd: __dirname + "/../runtime/outputs/" + id, stdio: "ignore" });
                        }

                        fs.rmSync("../runtime/outputs/" + id, { recursive: true });
                        listen();
                    } else {
                        process.stdout.clearLine(null);
                        process.stdout.cursorTo(0);
                        process.stdout.write("Waiting for generation to complete...");
                    }
                }
            });
        });
    }
}
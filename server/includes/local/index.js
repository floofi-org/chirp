#!/usr/bin/env node
const fs = require('fs');
const cp = require('child_process');
process.chdir(__dirname);
const readline = require('node:readline');
const { stdin: input, stdout: output } = require('node:process');

try { cp.execSync("pkill -9 python3"); } catch (e) {}
try { cp.execSync("pkill -9 Python"); } catch (e) {}

console.log('FVG local frontend');
console.log("(c) Equestria.dev Developers");
console.log("");

start("../MLPTTS");

function start() {
    // noinspection JSCheckFunctionSignatures
    let p = cp.exec("./run.sh", { stdio: "pipe", cwd: __dirname + "/../runtime" });
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
                            cp.execSync("mplayer audio.wav", { cwd: __dirname + "/../runtime/outputs/" + id, stdio: "ignore" });
                        }

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

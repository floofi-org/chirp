console.log("Loading...");

const fs = require('fs');
const path = require('path');
const cp = require('child_process');
let transcripts = "";
let index = 1;
let links = {};

console.log("Cleaning up previous output...");

if (fs.existsSync("out")) fs.rmSync("out", { recursive: true });

fs.mkdirSync("out");
fs.mkdirSync("out/pre1");
fs.mkdirSync("out/pre2");
fs.mkdirSync("out/wavs");

console.log("Importing Label Studio project...");
let prj = JSON.parse(fs.readFileSync("./project.json").toString());

for (let item of prj) {
    let fileName;
    
    if (!item?.annotations[0]?.result[0]?.value?.text) continue;
    
    if (item.data.audio.startsWith("https://cdn.equestria.dev/ssbt/")) {
        fileName = item.data.audio.substring(31);
    } else {
        fileName = item.data.audio.substring(6);
    }
    
    let directory = path.dirname(fileName);
    
    fs.mkdirSync("./out/" + directory, { recursive: true });
    
    try {
        if (item.data.audio.startsWith("https://cdn.equestria.dev/ssbt/")) {
            require('child_process').execSync("wget -O \"./out/pre1/" + index + ".wav\" \"" + item.data.audio + "\"");
        } else {
            fs.copyFileSync("/Users/raindrops/Library/Application Support/label-studio/media/upload/" + fileName, "./out/pre1/" + index + ".wav");
        }

        transcripts += index + ".wav|" + item.annotations[0].result[0].value.text + "\n";
        links[index + ".wav"] = index + ".wav";
        index++;
    } catch (e) {
        console.error(e);
    }
}

console.log("Exporting transcripts...");
let fail = false;

let ignored = [];

transcripts = transcripts.toString().trim().replaceAll("\r\n", "\n").split("\n").map((k) => {
    if (links[k.split("|")[0]]) {
        return k.split("|").map((i, j) => {
            if (j === 0) {
                return "wavs/" + (links[i] ?? i);
            }

            if (j === 1 || j === 2) {
                i = i.trim();
                if (!i.endsWith(".") && !i.endsWith("!") && !i.endsWith("?") && i.length > 0) {
                    console.log("[ERROR] Sentence in " + k.split("|")[0].split("_").slice(0, 2).join("/") + "/transcripts.txt for " + k.split("|")[0].split("_")[2] + " does not end with punctuation:\n    " + i);
                    fail = true;
                }
            }
            return i;
        }).join("|");
    } else {
        ignored.push(k.split("|")[0].split("_").join("/"));
        return null;
    }
}).filter(i => i).join("\n");

ignored = ignored.filter(i => !i.endsWith("/0.wav"));

if (ignored.length > 0) {
    console.log("[WARNING] The following " + ignored.length + " transcripts have been ignored due to their source file not existing: " + ignored.join(", "));
}

if (fail) {
    console.log("Not continuing because of errors mentioned above.");
    process.exit(2);
}

fs.writeFileSync("out/transcripts.txt", transcripts.split("\n").sort((a, b) => {
    let idA = parseInt(a.split("|")[0].split("wavs/")[1].split(".")[0]);
    let idB = parseInt(b.split("|")[0].split("wavs/")[1].split(".")[0]);
    
    return idA - idB;
}).join("\n").trim());

console.log("Processing wav files... part 1");
cp.execSync("python3.11 process1.py");

console.log("Processing wav files... part 2");
cp.execSync("arch -x86_64 python3.11 process2.py " + index);

console.log("Cleaning up...");

for (let file of fs.readdirSync("out")) {
    if (file.startsWith(".")) fs.rmSync("out/" + file, { recursive: true });
}

for (let file of fs.readdirSync("out/wavs")) {
    if (file.startsWith(".")) fs.rmSync("out/wavs/" + file, { recursive: true });
}

console.log("Exporting wavs archive...");
cp.execSync("zip -r wavs.zip wavs", { cwd: "./out" });
fs.rmSync("out/wavs", { recursive: true });
fs.rmSync("out/pre1", { recursive: true });
fs.rmSync("out/pre2", { recursive: true });
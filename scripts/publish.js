#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const readline = require('readline');
const { exec } = require('child_process');

const providerFile = path.resolve(__dirname, '../src/SharpServiceProvider.php');
const content = fs.readFileSync(providerFile, 'utf8');
const match = content.match(/VERSION = '(.*)'/);
const currentVersion = match ? match[1] : '';

const rl = readline.createInterface(process.stdin, process.stdout);

rl.question('Version: v', version => {
    console.log('modifying SharpServiceProvider.php...');
    writeVersion(version);
    exec(`git add ./src/SharpServiceProvider.php && git commit -m "Version" && git tag v${version}`, (err, stdout) => {
        if (err) {
            console.error(err);
            return;
        }
        console.log(stdout);
        console.log(`tag "v${version}" created`);
    });
});

rl.write(currentVersion);

function writeVersion(version) {
    const result = content.replace(/VERSION = '(.*)'/g, `VERSION = '${version}'`);
    fs.writeFileSync(providerFile, result, 'utf8');
}

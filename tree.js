#!/usr/bin/env node

const fs = require('node:fs');
const path = require('node:path');

const root = process.cwd();

const ignored = new Set([
    'node_modules',
    '.git',
    'vendor',
]);

function printTree(directory, prefix = '') {
    const entries = fs.readdirSync(directory, { withFileTypes: true })
        .filter(entry => !ignored.has(entry.name))
        .sort((a, b) => {
            if (a.isDirectory() !== b.isDirectory()) {
                return a.isDirectory() ? -1 : 1;
            }

            return a.name.localeCompare(b.name);
        });

    entries.forEach((entry, index) => {
        const isLast = index === entries.length - 1;
        const connector = isLast ? '    ' : '    ';

        console.log(`${prefix}${connector}${entry.name}${entry.isDirectory() ? '/' : ''}`);

        if (entry.isDirectory()) {
            printTree(
                path.join(directory, entry.name),
                prefix + (isLast ? '    ' : '    ')
            );
        }
    });
}

console.log(`${path.basename(root)}/`);
printTree(root);
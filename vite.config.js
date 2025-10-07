import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from "fs";
import path from "path";

const customDir = "resources/js/custom";
const customJsFiles = fs.readdirSync(customDir)
    .filter(file => file.endsWith(".js"))
    .map(file => path.join(customDir, file));

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                ...customJsFiles
            ],
            refresh: true,
        }),
    ],
});

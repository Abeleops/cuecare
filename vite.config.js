import { defineConfig } from 'vite';
import { createRequire } from 'node:module';
import { copyFileSync, existsSync } from 'node:fs';
import { resolve } from 'node:path';
import tailwindcss from '@tailwindcss/vite';

const require = createRequire(import.meta.url);
const laravel = require('laravel-vite-plugin').default;

const copyLaravelManifest = {
    name: 'copy-laravel-manifest',
    writeBundle() {
        const generatedManifest = resolve(
            process.cwd(),
            'public/build/.vite/manifest.json',
        );
        const LaravelManifest = resolve(
            process.cwd(),
            'public/build/manifest.json',
        );

        if (existsSync(generatedManifest)) {
            copyFileSync(generatedManifest, LaravelManifest);
        }
    },
};

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/sales.css',
                'resources/js/app.js',
                'resources/css/landing.css',
                'resources/js/landing.js',
                'resources/js/products.js',
                'resources/js/customers.js',
                'resources/js/staff.js',
                'resources/js/pos.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
        copyLaravelManifest,
    ],
    build: {
        manifest: 'manifest.json',
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

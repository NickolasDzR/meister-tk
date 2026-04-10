import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import path from "node:path";
import autoprefixer from 'autoprefixer';
import postcssSortMediaQueries from 'postcss-sort-media-queries';
import combineMediaQuery from 'postcss-combine-media-query';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/ts/app.ts',
                'resources/scss/app.scss',
            ],
            refresh: true,
        }),
        react(),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler',
                silenceDeprecations: ['legacy-js-api'],
                quietDeps: true, // оставляем, чтобы не видеть предупреждения из node_modules
            }
        },
        postcss: {
            plugins: [
                autoprefixer(),
                postcssSortMediaQueries({
                    sort: 'mobile-first'
                }),
                combineMediaQuery()
            ],
        },
    },
    resolve: {
        alias: {
            "~": path.resolve(__dirname, 'node_modules/'),
            '#': path.resolve(__dirname, 'resources'),
            "@components": path.resolve(__dirname, 'resources/views/components/'),
            "@modules": path.resolve(__dirname, 'resources/views/modules/'),
            // "@utils": path.resolve(__dirname, 'resources/ts/helpers/utils'),
            // "@types": path.resolve(__dirname, 'resources/ts/helpers/types'),

            '@': path.resolve(__dirname, 'resources/scripts'),
            '$': path.resolve(__dirname, 'resources/scss'),

            "@helpers": path.resolve(__dirname, 'resources/ts/helpers/'),
            "@types": path.resolve(__dirname, 'resources/ts/helpers/types'),
            "@utils": path.resolve(__dirname, 'resources/ts/helpers/utils'),

            '_fonts': path.resolve(__dirname, './resources/scss/base/_fonts.scss'),
            '_global': path.resolve(__dirname, './resources/scss/base/_global.scss'),

            '_grid': path.resolve(__dirname, './resources/scss/utils/_grid.scss'),
            '_variables': path.resolve(__dirname, './resources/scss/utils/_variables.scss'),
            '_vendor': path.resolve(__dirname, './resources/scss/utils/_vendor.scss'),

            // SCSS утилиты
            "@scss": path.resolve(__dirname, 'resources/scss'),

            // Библиотеки
            '@splidejs/splide': path.resolve(__dirname, 'node_modules/@splidejs/splide/dist/js/splide.esm.js'),
            '@splidecss': path.resolve(__dirname, 'node_modules/@splidejs/splide/dist/css/splide-core.min.css'),

            '@nice-select2-scss': path.resolve(__dirname, 'node_modules/nice-select2/src/scss/nice-select2.scss'),
            '@nice-select2': path.resolve(__dirname, 'resources/ts/modules/nice-select2.js'),
        }
    },
    server: {
        host: 'dev.local', // Важно для Docker
        port: 5173,
        hmr: {
            host: 'localhost',
            port: 5173,
        },
        cors: true,
        strictPort: true,
    },
    preview: {
        host: '0.0.0.0',
        port: 5173,
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
    },
});
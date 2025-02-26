import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import DefineOptions from 'unplugin-vue-define-options/vite';
import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';
import { PrimeVueResolver } from '@primevue/auto-import-resolver';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        DefineOptions(),
        AutoImport({
            imports: ['vue'],
        }),
        Components({
            resolvers: [PrimeVueResolver()]
        })
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vue: ['vue']
                }
            }
        }
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});

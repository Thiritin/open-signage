import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import svgLoader from 'vite-svg-loader';
// import legacy from '@vitejs/plugin-legacy'

export default defineConfig({
    build: {
         target: 'chrome70',
    },
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        svgLoader(),
        // legacy({
        //     targets: 'chrome >= 70',
        //     // ignoreBrowserslistConfig: true,
        //     // renderLegacyChunks: true,
        //     // renderModernChunks: true,
        //     // modernPolyfills: true,
        //     // additionalLegacyPolyfills: ['core-js/proposals/global-this'],
        //     // externalSystemJS: true,
        //     // modernPolyfills: ['es/global-this'],
        // })
    ],
});

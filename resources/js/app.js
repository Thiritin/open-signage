import './bootstrap.js';

import.meta.glob([
    './Projects/**/Assets/**',
]);

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import * as Sentry from "@sentry/vue";

const appName = 'Open Signage';
const appPath = import.meta.env.VITE_PROJECT_PATH;
import(`./Projects/${appPath}/app.css`);

createInertiaApp({
    title: (title) => `${appName}`,
    resolve: (name) => resolvePageComponent(`./${name}.vue`, import.meta.glob('./*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy);
        Sentry.init({
            app,
            dsn: "https://ee18fdfcc9b751a59e877bf305223229@o94350.ingest.sentry.io/4505788620341248"
        });
        app.mount(el);
        return app;
    },
});

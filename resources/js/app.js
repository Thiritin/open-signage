import "./bootstrap.js";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import * as Sentry from "@sentry/vue";
import Main from "./Main.vue";

import.meta.glob(["./Projects/**/Assets/**"]);

const appName = "Open Signage";
const appPath = import.meta.env.VITE_PROJECT_PATH;
import(`./Projects/${appPath}/app.css`);

createInertiaApp({
    title: (title) => `${appName}`,
    resolve: (name) => {
        return Main;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy);
        Sentry.init({
            app,
            dsn: "https://ee18fdfcc9b751a59e877bf305223229@o94350.ingest.sentry.io/4505788620341248",
        });
        app.mount(el);
        return app;
    },
});

String.prototype.truncate =
    String.prototype.truncate ||
    function (n, useWordBoundary) {
        if (this.length <= n) {
            return this;
        }
        const subString = this.slice(0, n - 1); // the original check
        return (
            (useWordBoundary
                ? subString.slice(0, subString.lastIndexOf(" "))
                : subString) + " …"
        );
    };

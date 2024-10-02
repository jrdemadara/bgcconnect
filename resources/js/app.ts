import { createApp, h, DefineComponent } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { createVfm } from "vue-final-modal";

import "./bootstrap";
import "../css/app.css";
import "vue-final-modal/style.css";

import Vue3Toastify, { type ToastContainerOptions } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Initialize vue-final-modal
        const vfm = createVfm();

        app.use(plugin)
            .use(ZiggyVue)
            .use(vfm)
            .use(Vue3Toastify, { autoClose: 3000 } as ToastContainerOptions)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});

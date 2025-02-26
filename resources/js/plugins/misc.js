import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

import { router, Link } from '@inertiajs/vue3'

export default {

    install(app) {

        app.use(Toast, {
            position: 'bottom-right',
            closeOnClick: true,
            showCloseButtonOnHover: true,
            draggable: true
        });

        const route = (name, params, absolute, config) => window.route(name, params, absolute, config);

        const visit = (name, params, options = {}) => router.visit(route(name, params), options);

        app.mixin({
            methods: { route, visit }
        });

        app.provide('route', route);
        app.provide('visit', visit);

        app.component('Link', Link);

    }
}
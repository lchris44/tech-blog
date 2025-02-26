import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { modal } from '/vendor/emargareten/inertia-modal';
import { createI18n } from 'vue-i18n';
import PrimeVue from 'primevue/config';
import Lara from '@primeuix/themes/lara';
import ConfirmationService from 'primevue/confirmationservice';

import DashboardLayout from '@/layout/Dashboard/AppLayout.vue';
import BlogLayout from '@/layout/Blog/AppLayout.vue';

import InputText from 'primevue/inputtext';
import VInputText from '@/components/VInputText.vue';
import VInputTextArea from '@/components/VInputTextArea.vue';
import VPassword from '@/components/VPassword.vue';
import VDataTable from '@/components/VDataTable.vue';
import VFormDialog from '@/components/VFormDialog.vue';
import VTextEditor from '@/components/VTextEditor/index.vue';
import VErrors from '@/components/VErrors.vue';

import misc from '@/plugins/misc';
import messages from '@/lang/messages.js';
import '@/assets/styles.scss';

// Globally import pages (excluding components)
const pages = import.meta.glob(['./Pages/**/*.vue', '!./Pages/**/Components/*.vue']);

// Retrieve stored locale from localStorage
const storedLocale = localStorage.getItem('locale');

// Initialize i18n for internationalization
const i18n = createI18n({
    locale: storedLocale || 'en', // Default to 'en' if no locale is stored
    fallbackLocale: 'en',
    legacy: false,
    globalInjection: true,
    messages,
});

// Resolve page components dynamically
const resolvePage = async (name) => await resolvePageComponent(`./Pages/${name}.vue`, pages);

// Initialize Inertia app
createInertiaApp({
    progress: {
        color: '#4B5563', // Progress bar color
    },
    resolve: async (name) => {
        // Resolve the page component
        const page = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));

        // Set custom layout based on the page name
        let customLayout = BlogLayout;
        if (name.includes('Dashboard')) {
            customLayout = DashboardLayout;
        }

        // Assign the layout to the page
        page.default.layout = page.default.layout || customLayout;

        return page;
    },
    setup({ el, App, props, plugin }) {
        // Create and configure the Vue app
        createApp({ render: () => h(App, props) })
            .use(modal, {
                resolve: async (name) => await resolvePage(name),
            })
            .use(plugin)
            .use(PrimeVue, {
                theme: {
                    preset: Lara,
                    options: {
                        darkModeSelector: '.app-dark',
                    },
                },
                ripple: true,
            })
            .use(ConfirmationService)
            .use(misc)
            .use(i18n)
            .component('InputText', InputText)
            .component('VInputText', VInputText)
            .component('VInputTextArea', VInputTextArea)
            .component('VPassword', VPassword)
            .component('VDataTable', VDataTable)
            .component('VFormDialog', VFormDialog)
            .component('VTextEditor', VTextEditor)
            .component('VErrors', VErrors)
            .mount(el);
    },
});
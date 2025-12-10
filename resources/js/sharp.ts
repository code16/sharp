import '../css/vendors.css';
import '../css/app.css';

import './polyfills';
import { createApp, DefineComponent, h, nextTick } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { progressDelay } from "@/api/progress";
import lucideIconsDefaultAttributes from 'lucide-vue-next/dist/esm/defaultAttributes';

import Form from "@/form/components/Form.vue";
import FormField from "@/form/components/Field.vue";
import ShowField from "@/show/components/Field.vue";
import { initRouter } from "@/router";

lucideIconsDefaultAttributes['aria-hidden'] = 'true'

initRouter();

createInertiaApp({
    resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({
            render: () => h(App, props),
        })
            .use(plugin);

        app.config.errorHandler = e => console.error(e);
        app.config.globalProperties.window = window;
        app.config.globalProperties.document = document;
        app.config.globalProperties.console = console;

        // declaring those globally because of circular dependencies
        app.component('SharpForm', Form);
        app.component('SharpFormField', FormField);
        app.component('SharpShowField', ShowField);

        app.mount(el);

        nextTick(() => {
            window.dispatchEvent(new CustomEvent('sharp:mounted'));
        });
    },
    progress: {
        delay: progressDelay,
        color: 'var(--progress-color)',
    }
});





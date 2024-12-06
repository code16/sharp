import '../css/vendors.css';
import '../css/app.css';

import { createApp, DefineComponent, h, nextTick } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { progressDelay } from "@/api/progress";
import lucideIconsDefaultAttributes from 'lucide-vue-next/dist/esm/defaultAttributes';

import Form from "@/form/components/Form.vue";
import FormField from "@/form/components/Field.vue";
import ShowField from "@/show/components/Field.vue";

lucideIconsDefaultAttributes['aria-hidden'] = 'true';

createInertiaApp({
    resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
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
        color: 'var(--nprogress-color, oklch(var(--primary-oklch)))',
    }
});

// force reload on previous navigation to invalidate outdated data / state
window.addEventListener('popstate', () => {
    document.addEventListener('inertia:navigate', () => {
        router.reload({ replace: true });
    }, { once: true });
});

// on server error (e.g. 500) we want to visit errored page for debugging purposes
router.on('invalid', event => {
    const response = event.detail.response;
    if(response.config.method.toLowerCase() === 'get') {
        location.href = event.detail.response.config.url;
    }
});





import { createApp, h, nextTick } from 'vue';

// import Notifications from 'vue-notification';


import { createInertiaApp, router } from "@inertiajs/vue3";
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

if(document.querySelector('[data-page]')) {
    createInertiaApp({
        resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
        setup({ el, App, props, plugin }) {
            const app = createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue, Ziggy);

            app.mount(el);

            nextTick(() => {
                window.dispatchEvent(new CustomEvent('sharp:mounted'));
            });
        },
    });

    // force reload on previous navigation to invalidate outdated data / state
    window.addEventListener('popstate', () => {
        document.addEventListener('inertia:navigate', () => {
            router.reload({ replace: true });
        }, { once: true });
    });
}





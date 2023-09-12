
import { createApp, h, nextTick } from 'vue';

// import Notifications from 'vue-notification';


// import { store as getStore } from './store/store';
import { createInertiaApp, router } from "@inertiajs/vue3";
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ignoredElements } from "@/utils/vue";


// const store = getStore();


if(document.querySelector('[data-page]')) {
    createInertiaApp({
        resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
        setup({ el, App, props, plugin }) {
            const app = createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue, Ziggy)
                .use(store);

            app.config.compilerOptions.isCustomElement = tag => ignoredElements.includes(tag);

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





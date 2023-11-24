import { createApp, DefineComponent, h, nextTick } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

createInertiaApp({
    resolve: name => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin);

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





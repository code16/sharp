import routeFn, { type Config } from 'ziggy-js';

declare global {
    const Ziggy: Config;
    const route: typeof routeFn;
}

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        route: typeof routeFn;
    }
}

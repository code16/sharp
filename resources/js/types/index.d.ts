


export * from './generated.d';

declare module '@inertiajs/core' {
    export interface PageProps {
        locale: string;
    }
}
declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        window: Window;
        document: Document;
        console: Console;
    }
}

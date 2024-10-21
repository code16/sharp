import { SessionData } from "@/types/generated";


export * from './generated.d';

export type SharpPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    locale: string;
    session: SessionData;
};

declare module '@inertiajs/core' {
    interface PageProps extends SharpPageProps {
        [key: string]: unknown;
    }
}

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        window: Window;
        document: Document;
        console: Console;
    }
}

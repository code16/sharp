import { SessionData } from "@/types/generated";


export * from './generated.d';

export type SharpPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    locale: string;
    session: SessionData;
    query: {
        popstate?: '1',
        highlighted_entity_key?: string,
        highlighted_instance_id?: string,
    },
};

declare module '@inertiajs/core' {
    interface PageProps extends SharpPageProps {
        [key: string]: unknown;
    }
}

declare module 'vue' {
    interface ComponentCustomProperties {
        window: Window;
        document: Document;
        console: Console;
    }
}

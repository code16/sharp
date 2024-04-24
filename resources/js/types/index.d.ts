

export * from './generated.d';

declare module '@inertiajs/core' {
    export interface PageProps {
        locale: string;
    }
}

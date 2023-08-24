import { Config, RouteParam, RouteParamsWithQueryOverload, Router } from 'ziggy-js';

export type ZiggyRouter = Omit<Router, 'params'> & { get params(): { [key: string]: string } }

declare global {
    const Ziggy: Config;
    function route(
        name?: undefined,
        params?: RouteParamsWithQueryOverload | RouteParam,
        absolute?: boolean,
        config?: Config,
    ): ZiggyRouter;
    function route(
        name: string,
        params?: RouteParamsWithQueryOverload | RouteParam,
        absolute?: boolean,
        config?: Config,
    ): string;
}

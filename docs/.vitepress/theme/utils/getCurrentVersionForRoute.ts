import versions from '../../../versions.json';
import type { Route } from "vitepress";

export function getCurrentVersionForRoute(route: Route)
{
    return versions.find(version => version.slug === route.data.relativePath.replace(/^\//, '').split('/')[0])
}

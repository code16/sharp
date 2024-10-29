import { Loader } from "@googlemaps/js-api-loader";

let loaded = false;
export function loadGmaps(apiKey: string) {
    if(!loaded) {
        loaded = true;

        const loader = new Loader({
            apiKey,
            version: "quarterly",
            // libraries: ["maps", "geocoding"],
        });
        return loader.importLibrary('maps');
    }
}

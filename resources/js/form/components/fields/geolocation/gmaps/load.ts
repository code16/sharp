import { Loader } from "@googlemaps/js-api-loader";

let loaded = false;
export async function loadGmaps(apiKey: string) {
    if(!loaded) {
        loaded = true;
        const loader = new Loader({
            apiKey,
            version: "quarterly",
            // libraries: ["maps", "geocoding"],
        });
        await loader.importLibrary('core');
    }
}

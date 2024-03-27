import { loadGMapApi } from '@fawmi/vue-google-maps/src/load-google-maps';

let loaded = false;
export function loadGmaps(apiKey) {
    if(!loaded) {
        loadGMapApi({
            v: 3,
            key: apiKey,
        });
        loaded = true;

        return new Promise((resolve) => {
            window['vueGoogleMapsInit'] = resolve
        });
    }
}

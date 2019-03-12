import Vue from 'vue';
import { loadGmapApi } from 'vue2-google-maps';

export default function gmapsLoader({ apiKey }) {
    const options = { v: 3 };

    if(apiKey) {
        options.key = apiKey;
    }
    loadGmapApi(options);

    // https://github.com/xkjyeah/vue-google-maps/blob/vue2/src/main.js
    return Vue.$gmapApiPromiseLazy();
}
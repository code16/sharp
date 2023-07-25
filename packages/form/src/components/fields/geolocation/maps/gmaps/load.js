// import Vue from 'vue';
import once from 'lodash/once';
import { loadGmapApi } from 'sharp/vendor/vue2-google-maps';

export default once(({ apiKey }) => {
    const options = { v: 3 };

    if(apiKey) {
        options.key = apiKey;
    }
    loadGmapApi(options);

    // todo update google maps
    // https://github.com/xkjyeah/vue-google-maps/blob/vue2/src/main.js
    // return Vue.$gmapApiPromiseLazy();
});

import Gmaps from './gmaps/Gmaps.vue';
import GmapsEditable from './gmaps/GmapsEditable.vue';
import Osm from './osm/Osm.vue';
import OsmEditable from './osm/OsmEditable.vue';
import loadGmaps from './gmaps/loader';
import gmapsGeocode from './gmaps/geocoder';

export function getMapByProvider(provider) {
    if(provider === 'gmaps') {
        return Gmaps;
    } else if(provider === 'osm') {
        return Osm;
    }
}

export function getEditableMapByProvider(provider) {
    if(provider === 'gmaps') {
        return GmapsEditable;
    } else if(provider === 'osm') {
        return OsmEditable;
    }
}

export function loadMapProvider(provider, options) {
    if(provider === 'gmaps') {
        return loadGmaps(options);
    }
    return Promise.resolve();
}

export function geocode(provider, address) {
    if(provider === 'gmaps') {
        return gmapsGeocode(address);
    }
    return Promise.resolve([]);
}
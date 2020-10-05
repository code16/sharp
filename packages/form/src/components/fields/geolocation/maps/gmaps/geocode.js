import { normalizeBounds } from "./util";

let geocoder = null;

export default function gmapsGeocode({ address, latLng }) {
    if(!geocoder) {
        geocoder = new google.maps.Geocoder();
    }
    return new Promise((resolve, reject) => {
        geocoder.geocode({ address, location:latLng }, (results, status) => {
            if(status === 'OK') {
                resolve(
                    results.map(result => ({
                        location: result.geometry.location.toJSON(),
                        bounds: normalizeBounds(result.geometry.viewport),
                        address: result.formatted_address,
                    }))
                );
            } else if(status === 'ZERO_RESULTS') {
                resolve([]);
            } else {
                reject(status);
            }
        });
    });
}
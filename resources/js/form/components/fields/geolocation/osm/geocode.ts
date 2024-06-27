import axios from 'axios';
import { GeocodeParams, GeocodeResult } from "../types";

//https://wiki.openstreetmap.org/wiki/Nominatim

function getResults(address) {
    return axios.get('https://nominatim.openstreetmap.org/search', {
        params: {
            q: address,
            format: 'json',
        }
    }).then(response => response.data);
}

function getReverseResults(latLng) {
    return axios.get('https://nominatim.openstreetmap.org/reverse', {
        params: {
            lat: latLng.lat,
            lon: latLng.lng,
            format: 'json',
        }
    }).then(response => [response.data]);
}

async function osmGeocode(params: GeocodeParams): Promise<GeocodeResult[]> {
    const results = params.latLng
        ? await getReverseResults(params.latLng)
        : await getResults(params.address);

    return results.map(result => ({
        location: {
            lat: Number(result.lat),
            lng: Number(result.lon),
        },
        bounds: [
            { lat: Number(result.boundingbox[0]), lng: Number(result.boundingbox[2]) },
            { lat: Number(result.boundingbox[1]), lng: Number(result.boundingbox[3]) },
        ],
        address: result.display_name,
    }) satisfies GeocodeResult);
}

export default osmGeocode;

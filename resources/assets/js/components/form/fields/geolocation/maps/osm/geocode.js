import axios from 'axios';

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

async function osmGeocode({ address, latLng }) {
    const results = !!latLng
        ? await getReverseResults(latLng)
        : await getResults(address);

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
    }));
}

export default osmGeocode;
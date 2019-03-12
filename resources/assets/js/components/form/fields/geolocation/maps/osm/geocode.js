import axios from 'axios';


async function osmGeocode(address) {
    const results = await axios.get('https://nominatim.openstreetmap.org/search', {
        params: {
            q: address,
            format: 'json',
        }
    });
    return results.map(result => ({
        location: {
            lat: result.lat,
            lng: result.lng,
        },
        bounds: [
            { lat: result.boundingbox[0], lng: result.boundingbox[2] },
            { lat: result.boundingbox[1], lng: result.boundingbox[3] },
        ],
    }));
}

export default osmGeocode;
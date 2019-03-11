
let geocoder = null;

export default function geocode(address) {
    if(!geocoder) {
        geocoder = new google.maps.Geocoder();
    }
    return new Promise((resolve, reject) => {
        geocoder.geocode({ address }, (results, status) => {
            if(status === 'OK') resolve(
                results.map(result => ({
                    location: result.geometry.location,
                    bounds: result.geometry.viewport,
                }))
            );
            else reject(status);
        });
    });
}
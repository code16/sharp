/// <reference types="@types/google.maps" />

import { GeocodeParams, GeocodeResult } from "../types";
import { loadGmaps } from "@/form/components/fields/geolocation/gmaps/load";

let geocoder = null;

export default async function gmapsGeocode(apiKey: string, params: GeocodeParams): Promise<GeocodeResult[]> {
    if(!geocoder) {
        await loadGmaps(apiKey);
        const { Geocoder } = await google.maps.importLibrary('geocoding') as google.maps.GeocodingLibrary;
        geocoder = new Geocoder();
    }
    return new Promise((resolve, reject) => {
        geocoder.geocode(
            { address: params.address, location: params.latLng },
            (results: google.maps.GeocoderResult[], status: google.maps.GeocoderStatus) => {
                if(status === 'OK') {
                    resolve(
                        results.map(result => ({
                            location: result.geometry.location.toJSON(),
                            bounds: [
                                result.geometry.viewport.getSouthWest().toJSON(),
                                result.geometry.viewport.getNorthEast().toJSON(),
                            ],
                            address: result.formatted_address,
                        }) satisfies GeocodeResult)
                    );
                } else if(status === 'ZERO_RESULTS') {
                    resolve([]);
                } else {
                    reject(status);
                }
            }
        );
    });
}

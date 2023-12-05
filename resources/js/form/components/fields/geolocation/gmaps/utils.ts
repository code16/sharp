/// <reference types="@types/google.maps" />
import { Bounds } from "../types";


export function gmapsMapOptions(maxBounds: Bounds | null, options: google.maps.MapOptions): google.maps.MapOptions {
    return {
        ...options,
        restriction: maxBounds ? {
            latLngBounds: new google.maps.LatLngBounds(maxBounds[0], maxBounds[1])
        } : null,
    }
}

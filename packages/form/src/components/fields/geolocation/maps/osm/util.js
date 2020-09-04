import { latLngBounds } from 'leaflet';

export function normalizeBounds(latLngBounds) {
    const sw = latLngBounds.getSouthWest();
    const ne = latLngBounds.getNorthEast();
    return [
        { lat: sw.lat, lng: sw.lng },
        { lat: ne.lat, lng: sw.lng },
    ];
}

export function toLatLngBounds(normalizedBounds) {
    const bounds = normalizedBounds;
    return Array.isArray(bounds)
        ? latLngBounds(bounds[0], bounds[1])
        : null;
}
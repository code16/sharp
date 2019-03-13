

export const defaultMapOptions = {
    mapTypeControl: false,
    streetViewControl: false
};

export function normalizeBounds(latLngBounds) {
    return [
        latLngBounds.getSouthWest().toJSON(),
        latLngBounds.getNorthEast().toJSON(),
    ];
}

export function toLatLngBounds(normalizedBounds) {
    const bounds = normalizedBounds;
    return Array.isArray(bounds)
        ? new google.maps.LatLngBounds(bounds[0], bounds[1])
        : null;
}
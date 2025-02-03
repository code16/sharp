import { FormGeolocationFieldData } from "@/types";

export type LatLng = { lat: number, lng: number };

export type GeocodeParams = { address?: string, latLng?: LatLng };

export type GeocodeResult = {
    location: LatLng,
    bounds: Bounds,
    address: string,
};

export type Bounds = [
    { lat: number, lng: number },
    { lat: number, lng: number },
];

export type MapComponentProps = {
    field: FormGeolocationFieldData,
    markerPosition: FormGeolocationFieldData['value'] | null,
    center: FormGeolocationFieldData['value'] | null,
    maxBounds: Bounds | null,
    bounds?: Bounds | null,
    zoom: number,
    editable?: boolean,
}

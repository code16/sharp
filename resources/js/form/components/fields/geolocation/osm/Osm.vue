<script setup lang="ts">
    import { LMap, LTileLayer, LMarker } from '@vue-leaflet/vue-leaflet';
    import { Icon, latLngBounds } from 'leaflet';
    import iconUrl from 'leaflet/dist/images/marker-icon.png';
    import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
    import shadowUrl from 'leaflet/dist/images/marker-shadow.png';
    import { MapComponentProps } from "../types";

    Icon.Default.mergeOptions({
        iconUrl,
        iconRetinaUrl,
        shadowUrl,
    });

    defineProps<MapComponentProps>();
</script>

<template>
    <LMap
        v-if="field.mapsProvider.name === 'osm'"
        :zoom="zoom"
        :center="center"
        :max-bounds="maxBounds ? latLngBounds(maxBounds) : null"
    >
        <LTileLayer :url="field.mapsProvider.options?.tilesUrl ?? 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'" />
        <LMarker :lat-lng="markerPosition" />
    </LMap>
</template>

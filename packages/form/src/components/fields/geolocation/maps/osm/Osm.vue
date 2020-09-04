<template>
    <LMap
        :zoom="zoom"
        :center="center"
        :max-bounds="transformedMaxBounds"
    >
        <LTileLayer :url="tilesUrl" />
        <LMarker :lat-lng="markerPosition" />
    </LMap>
</template>

<script>
    import { LMap, LTileLayer, LMarker } from 'vue2-leaflet';
    import { toLatLngBounds } from './util';

    export default {
        name: 'SharpOsm',
        components: {
            LMap,
            LMarker,
            LTileLayer,
        },
        props: {
            markerPosition: Object,
            zoom: Number,
            center: Object,
            maxBounds: Array,
            tilesUrl: {
                type: String,
                default: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
            },
        },
        computed: {
            transformedMaxBounds() {
                return toLatLngBounds(this.maxBounds);
            },
        },
    }
</script>
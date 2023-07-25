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
    import { LMap, LTileLayer, LMarker } from '@vue-leaflet/vue-leaflet';
    import { toLatLngBounds } from './util';
    import iconUrl from 'leaflet/dist/images/marker-icon.png';
    import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
    import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

    import { Icon } from 'leaflet';

    Icon.Default.mergeOptions({
        iconUrl,
        iconRetinaUrl,
        shadowUrl,
    });

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

<template>
    <LMap
        :zoom="zoom"
        :center="center"
        :bounds="transformedBounds"
        :max-bounds="transformedMaxBounds"
        @click="handleMapClicked"
    >
        <LTileLayer :url="tilesUrl" />
        <template v-if="hasMarker">
            <LMarker :lat-lng="markerPosition" draggable @dragend="handleMarkerDragEnd" />
        </template>
    </LMap>
</template>

<script>
    import { LMap, LTileLayer, LMarker } from '@vue-leaflet/vue-leaflet';
    import { toLatLngBounds } from "./util";
    import { triggerResize } from "../../util";

    export default {
        name: 'SharpOsmEditable',
        components: {
            LMap,
            LMarker,
            LTileLayer,
        },
        props: {
            markerPosition: Object,
            center: Object,
            zoom: Number,
            bounds: Array,
            maxBounds: Array,
            tilesUrl: {
                type: String,
                default: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
            },
        },
        computed: {
            hasMarker() {
                return !!this.markerPosition;
            },
            transformedBounds() {
                return toLatLngBounds(this.bounds);
            },
            transformedMaxBounds() {
                return toLatLngBounds(this.maxBounds);
            }
        },
        methods: {
            handleMapClicked(e) {
                this.$emit('change', e.latlng);
            },
            handleMarkerDragEnd(e) {
                this.$emit('change', e.target.getLatLng());
            },
        },
        mounted() {
            triggerResize();
        },
    }
</script>

<template>
    <LMap
        style="padding-bottom: 80%"
        :zoom="zoom"
        :center="center"
        :bounds="transformedBounds"
        @click="handleMapClicked"
    >
        <LTileLayer :url="tilesUrl" />
        <template v-if="hasMarker">
            <LMarker :lat-lng="markerPosition" />
        </template>
    </LMap>
</template>

<script>
    import { latLngBounds } from 'leaflet';
    import { LMap, LTileLayer, LMarker } from 'vue2-leaflet';

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
                return Array.isArray(this.bounds)
                    ? latLngBounds(this.bounds[0], this.bounds[1])
                    : null;
            }
        },
        methods: {
            handleMapClicked(e) {
                this.$emit('map-click', e.latlng);
            }
        }
    }
</script>
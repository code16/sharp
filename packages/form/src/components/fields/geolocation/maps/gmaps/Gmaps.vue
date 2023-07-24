<template>
    <GmapMap
        :center="center"
        :zoom="zoom"
        :options="options"
        ref="map"
    >
        <GmapMarker :position="markerPosition" />
    </GmapMap>
</template>

<script>
    import { Map, Marker } from 'sharp/vendor/vue2-google-maps';
    import { defaultMapOptions, createMapOptions } from "./util";

    export default {
        name: 'SharpGmaps',

        components: {
            GmapMap: Map,
            GmapMarker: Marker,
        },

        props: {
            markerPosition: Object,
            center: Object,
            zoom: Number,
        },

        computed: {
            options() {
                return createMapOptions({
                    ...defaultMapOptions,
                    maxBounds: this.maxBounds,
                    controlSize: 32,
                });
            },
        },
    }
</script>

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
    import { Map, Marker, loadGmapApi } from 'vue2-google-maps';
    import { defaultMapOptions } from "./util";

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
                return {
                    ...defaultMapOptions,
                };
            },
        },
    }
</script>
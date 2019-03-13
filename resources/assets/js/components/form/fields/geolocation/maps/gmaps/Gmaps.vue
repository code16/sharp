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

        inject: ['$tab'],

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

        methods:{
            refresh() {
                if(this.$refs.map) {
                    google.maps.event.trigger(this.$refs.map.$mapObject, 'resize');
                }
                else console.log('Geolocation : no $refs map');
            },
        },

        mounted() {
            if(this.$tab) {
                this.$tab.$on('active', this.refresh);
            }
        },
    }
</script>
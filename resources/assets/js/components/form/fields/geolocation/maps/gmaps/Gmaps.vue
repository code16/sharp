<template>
    <GmapMap
        :center="position"
        :zoom="zoom"
        :options="options"
        style="padding-bottom: 80%"
        class="mw-100"
        ref="map"
    >
        <GmapMarker :position="position" />
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
            position: Object,
            bounds: Object,
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
            handlePositionChanged(value) {
                this.$emit('input', value.toJson());
            },

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
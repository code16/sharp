<template>
    <GmapMap
        :center="center"
        :zoom="zoom"
        :options="options"
        @click="handleMapClicked"
        ref="map"
    >
        <template v-if="hasMarker">
            <GmapMarker :position="markerPosition" draggable @dragend="handleMarkerDragEnd" />
        </template>
    </GmapMap>
</template>

<script>
    import { Map, Marker } from 'sharp/vendor/vue2-google-maps';
    import { defaultMapOptions, toLatLngBounds, createMapOptions } from "./util";

    export default {
        name: 'SharpGmapsEditable',

        components: {
            GmapMap: Map,
            GmapMarker: Marker,
        },

        props: {
            markerPosition: Object,
            bounds: Array,
            center: Object,
            zoom: Number,
            maxBounds: Array,
        },

        computed: {
            options() {
                return createMapOptions({
                    ...defaultMapOptions,
                    maxBounds: this.maxBounds,
                    draggableCursor: 'crosshair',
                });
            },
            hasMarker() {
                return !!this.markerPosition;
            }
        },
        watch: {
            bounds(bounds) {
                const latLngBounds = toLatLngBounds(bounds);
                if(latLngBounds) {
                    this.$refs.map.$mapObject.fitBounds(latLngBounds);
                }
            },
        },

        methods: {
            handleMapClicked(e) {
                this.$emit('change', e.latLng.toJSON());
            },
            handleMarkerDragEnd(e) {
                this.$emit('change', e.latLng.toJSON());
            }
        },
    }
</script>

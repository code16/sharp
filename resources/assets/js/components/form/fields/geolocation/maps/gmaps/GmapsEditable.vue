<template>
    <GmapMap
        class="mw-100"
        style="padding-bottom: 80%;"
        :center="center"
        :zoom="zoom"
        :options="options"
        @click="handleMapClicked"
        ref="map"
    >
        <GmapMarker
            v-if="position"
            :position="position"
            draggable
        >
        </GmapMarker>
    </GmapMap>
</template>

<script>
    import { Map, Marker } from 'vue2-google-maps';
    import { defaultMapOptions } from "./util";

    export default {
        name: 'SharpGmapsEditable',

        components: {
            GmapMap: Map,
            GmapMarker: Marker,
        },

        props: {
            position: Object,
            bounds: Object,
            center: {
                type: Object,
                default: ()=>({
                    lat: 46.1445458,
                    lng: -2.4343779
                })
            },
            zoom: {
                type: Number,
                default:4
            },
        },

        computed: {
            options() {
                return {
                    ...defaultMapOptions,
                }
            }
        },

        methods: {
            handleMapClicked(e) {
                this.$emit('map-click', e.latLng);
            },
        },
    }
</script>
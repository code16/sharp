<script setup lang="ts">
    import GmapsMap from '@fawmi/vue-google-maps/src/components/map.vue';
    import GmapsMarker from '@fawmi/vue-google-maps/src/components/marker.vue';
    import { MapComponentProps } from "../types";
    import { gmapsMapOptions } from "./utils";

    defineProps<MapComponentProps>();
</script>

<template>
    <GmapsMap
        :center="center"
        :zoom="zoom"
        :options="gmapsMapOptions(maxBounds, {
            mapTypeControl: false,
            streetViewControl: false,
            controlSize: 32,
        })"
    >
        <GmapsMarker :position="markerPosition" />
    </GmapsMap>
</template>

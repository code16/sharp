<script setup lang="ts">
    /// <reference types="@types/google.maps" />
    import GmapsMap from '@fawmi/vue-google-maps/src/components/map.vue';
    import GmapsMarker from '@fawmi/vue-google-maps/src/components/marker.vue';
    import { EditableMapComponentProps } from "../types";
    import { ref, watch } from "vue";
    import { gmapsMapOptions } from "./utils";

    const props = defineProps<EditableMapComponentProps>();

    const emit = defineEmits(['change']);
    const map = ref<{ $mapObject: google.maps.Map }>();

    function onClick(e: google.maps.MapMouseEvent) {
        emit('change', e.latLng.toJSON());
    }

    function onMarkerDragEnd(e: google.maps.MapMouseEvent) {
        emit('change', e.latLng.toJSON());
    }

    watch(() => props.bounds, bounds => {
        if(bounds) {
            map.value.$mapObject.fitBounds(new google.maps.LatLngBounds(bounds[0], bounds[1]));
        }
    });
</script>

<template>
    <GmapsMap
        :center="center"
        :zoom="zoom"
        :options="gmapsMapOptions(maxBounds, {
            mapTypeControl: false,
            streetViewControl: false,
            draggableCursor: 'crosshair',
        })"
        @click="onClick"
        ref="map"
    >
        <template v-if="markerPosition">
            <GmapsMarker :position="markerPosition" draggable @dragend="onMarkerDragEnd" />
        </template>
    </GmapsMap>
</template>

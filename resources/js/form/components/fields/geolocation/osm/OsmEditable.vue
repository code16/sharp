<script setup lang="ts">
    import { LMap, LTileLayer, LMarker } from '@vue-leaflet/vue-leaflet';
    import { DragEndEvent, latLngBounds, LeafletMouseEvent, Marker } from 'leaflet';
    import { EditableMapComponentProps } from "../types";
    import { onMounted } from "vue";
    import { triggerResize } from "../utils";

    defineProps<EditableMapComponentProps>();

    const emit = defineEmits(['change']);

    function onClick(e: LeafletMouseEvent) {
        emit('change', e.latlng);
    }

    function onMarkerDragEnd(e: DragEndEvent) {
        emit('change', (e.target as Marker).getLatLng());
    }

    onMounted(() => {
        triggerResize();
    });
</script>

<template>
    <LMap
        v-if="field.mapsProvider.name === 'osm'"
        :zoom="zoom"
        :center="center"
        :bounds="latLngBounds(bounds)"
        :max-bounds="maxBounds ? latLngBounds(maxBounds) : null"
        @click="onClick"
    >
        <LTileLayer :url="field.mapsProvider.options?.tilesUrl ?? 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'" />
        <template v-if="markerPosition">
            <LMarker :lat-lng="markerPosition" draggable @dragend="onMarkerDragEnd" />
        </template>
    </LMap>
</template>

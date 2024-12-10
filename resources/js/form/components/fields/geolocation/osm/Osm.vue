<script setup lang="ts">
    import type { DragEndEvent, LeafletMouseEvent, Marker, MapOptions, TileLayerOptions } from 'leaflet';
    import iconUrl from 'leaflet/dist/images/marker-icon.png';
    import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
    import shadowUrl from 'leaflet/dist/images/marker-shadow.png';
    import { MapComponentProps } from "../types";

    const { LMap, LTileLayer, LMarker } = await import('@vue-leaflet/vue-leaflet');
    const { Icon, latLngBounds } = await import('leaflet');

    Icon.Default.mergeOptions({
        iconUrl,
        iconRetinaUrl,
        shadowUrl,
    });

    const props = defineProps<MapComponentProps>();
    const emit = defineEmits(['change']);

    function onClick(e: LeafletMouseEvent) {
        if(props.editable) {
            emit('change', e.latlng);
        }
    }

    function onMarkerDragEnd(e: DragEndEvent) {
        emit('change', (e.target as Marker).getLatLng());
    }
</script>

<template>
    <LMap
        v-if="field.mapsProvider.name === 'osm'"
        class="!h-auto"
        :class="editable ? 'cursor-crosshair' : ''"
        v-bind="{
            zoom,
            center: center ? [center.lat, center.lng] : null,
            maxBounds: maxBounds ? latLngBounds(maxBounds) : null,
        } satisfies MapOptions"
        :bounds="bounds ? latLngBounds(bounds) : null"
        @click="onClick"
    >
        <LTileLayer
            :url="field.mapsProvider.options?.tilesUrl ?? 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'"
            v-bind="{
                detectRetina: true,
            } satisfies TileLayerOptions"
        />
        <template v-if="markerPosition">
            <LMarker :lat-lng="markerPosition" :draggable="editable" @dragend="onMarkerDragEnd" />
        </template>
    </LMap>
</template>

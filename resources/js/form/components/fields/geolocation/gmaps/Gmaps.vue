<script setup lang="ts">
    import { MapComponentProps } from "../types";
    import { onMounted, ref, useTemplateRef, watch, watchEffect } from "vue";

    const props = defineProps<MapComponentProps>();
    const emit = defineEmits(['change']);

    const map = ref<google.maps.Map>();
    const marker = ref<google.maps.marker.AdvancedMarkerElement>();
    const mapContainer = useTemplateRef<HTMLElement>('mapContainer');

    onMounted(async () => {
        const { Map } = await google.maps.importLibrary('maps') as google.maps.MapsLibrary;
        const { AdvancedMarkerElement } = await google.maps.importLibrary('marker') as google.maps.MarkerLibrary;

        map.value = new Map(mapContainer.value, {
            center: props.center,
            zoom: props.zoom,
            mapTypeControl: false,
            streetViewControl: false,
            mapId: props.field.mapsProvider.name === 'gmaps' && props.field.mapsProvider.options.mapId,
            restriction: props.maxBounds ? {
                latLngBounds: new google.maps.LatLngBounds(props.maxBounds[0], props.maxBounds[1])
            } : null,
            ...props.editable ? {
                draggableCursor: 'crosshair',
            } : {
                controlSize: 32,
            },
        });

        marker.value = new AdvancedMarkerElement({
            map: map.value,
            position: props.markerPosition,
            gmpDraggable: props.editable,
        });

        if(props.editable) {
            map.value.addListener('click', (e: google.maps.MapMouseEvent) => {
                emit('change', e.latLng.toJSON());
            })
            marker.value.addListener('dragend', (e: google.maps.MapMouseEvent) => {
                emit('change', e.latLng.toJSON());
            });
        }
    });
    watchEffect(() => {
        map.value?.setOptions({
            center: props.center,
            zoom: props.zoom,
        });
    });
    watchEffect(() => {
        if(props.bounds) {
            map.value.fitBounds(new google.maps.LatLngBounds(props.bounds[0], props.bounds[1]));
        }
    });
    watchEffect(() => {
        if(marker.value) {
            marker.value.position = props.markerPosition;
        }
    });
</script>

<template>
    <div ref="mapContainer"></div>
</template>

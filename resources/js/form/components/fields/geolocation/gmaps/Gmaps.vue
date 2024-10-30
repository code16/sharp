<script setup lang="ts">
    import { MapComponentProps } from "../types";
    import { onMounted, shallowRef, useTemplateRef, watch, watchEffect } from "vue";
    import { loadGmaps } from "@/form/components/fields/geolocation/gmaps/load";

    const props = defineProps<MapComponentProps>();
    const emit = defineEmits(['change']);

    const map = shallowRef<google.maps.Map>();
    const marker = shallowRef<google.maps.marker.AdvancedMarkerElement>();
    const mapContainer = useTemplateRef<HTMLElement>('mapContainer');

    await loadGmaps(props.field.mapsProvider.name === 'gmaps' && props.field.mapsProvider.options.apiKey);

    const { Map } = await google.maps.importLibrary('maps') as google.maps.MapsLibrary;
    const { AdvancedMarkerElement } = await google.maps.importLibrary('marker') as google.maps.MarkerLibrary;

    onMounted(() => {
         map.value = new Map(mapContainer.value, {
             center: props.center,
             zoom: props.zoom,
             mapTypeControl: false,
             clickableIcons: false,
             streetViewControl: false,
             mapId: props.field.mapsProvider.name === 'gmaps' && props.field.mapsProvider.options.mapId,
             restriction: props.maxBounds ? {
                 latLngBounds: new google.maps.LatLngBounds(props.maxBounds[0], props.maxBounds[1])
             } : null,
             ...props.editable ? {
                 draggableCursor: 'crosshair',
             } : {
                 controlSize: 32,
                 zoomControl: false,
             },
         });

        marker.value = new AdvancedMarkerElement({
            map: map.value,
            position: props.markerPosition,
            gmpDraggable: props.editable,
        });
        watchEffect(() => {
            marker.value.position = props.markerPosition;
        });

        if(props.editable) {
            marker.value.addListener('dragend', (e: google.maps.MapMouseEvent) => {
                emit('change', e.latLng.toJSON());
            });
        }

        if(props.editable) {
            map.value.addListener('click', (e: google.maps.MapMouseEvent) => {
                emit('change', e.latLng.toJSON());
            })
        }
    });
    watch(() => [props.center, props.zoom], () => {
        map.value?.setOptions({
            center: props.center,
            zoom: props.zoom,
        });
    });
    watch(() => props.bounds, () => {
        if(props.bounds) {
            map.value?.fitBounds(new google.maps.LatLngBounds(props.bounds[0], props.bounds[1]));
        }
    });
</script>

<template>
    <div ref="mapContainer"></div>
</template>

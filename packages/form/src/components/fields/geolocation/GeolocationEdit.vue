<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import TextInput from '../text/TextInput.vue';
    import { FormGeolocationFieldData } from "@/types";
    import { Component, ref } from "vue";
    import GmapsEditable from "./gmaps/GmapsEditable.vue";
    import OsmEditable from "./osm/OsmEditable.vue";
    import { Loading, Button } from '@sharp/ui';
    import { EditableMapComponentProps, GeocodeParams, LatLng } from "./types";
    import gmapsGeocode from "./gmaps/geocode";
    import osmGeocode from "./osm/geocode";

    const props = defineProps<{
        field: FormGeolocationFieldData,
        value: FormGeolocationFieldData['value'],
        markerPosition: LatLng,
    }>();

    const emit = defineEmits(['change']);

    const search = ref();
    const message = ref();
    const loading = ref(false);
    const bounds = ref();

    const components: Record<FormGeolocationFieldData['mapsProvider']['name'], Component> = {
        gmaps: GmapsEditable,
        osm: OsmEditable,
    };

    function geocode(params: GeocodeParams) {
        if(props.field.geocodingProvider.name === 'gmaps') {
            return gmapsGeocode(params);
        }
        return osmGeocode(params);
    }

    function onSearchSubmit() {
        const address = search.value;
        message.value = '';
        loading.value = true;
        geocode({ address: search.value })
            .then(results => {
                if(results.length > 0) {
                    bounds.value = results[0].bounds;
                    emit('change', results[0].location);
                } else {
                    message.value = __('sharp::form.geolocation.modal.geocode_input.message.no_results', { query: address ?? '' });
                }
            })
            .catch(status => {
                message.value = `${__(`sharp::form.geolocation.modal.geocode_input.message.error`)}${status?` (${status})`:''}`;
            })
            .finally(() => {
                loading.value = false;
            });
    }

    function onMarkerPositionChange(position: LatLng) {
        message.value = '';
        emit('change', position);

        if(props.field.geocoding) {
            loading.value = true;
            geocode({ latLng: position })
                .then(results => {
                    if(results.length > 0) {
                        search.value = results[0].address;
                    }
                })
                .finally(() => {
                    loading.value = false;
                });
        }
    }
</script>

<template>
    <div>
        <template v-if="field.geocoding">
            <div class="mb-2">
                <form @submit.prevent="onSearchSubmit">
                    <div class="flex">
                        <div class="relative">
                            <TextInput
                                v-model="search"
                                :placeholder="__('sharp::form.geolocation.modal.geocode_input.placeholder')"
                            />
                            <Loading class="absolute right-0 -translate-y-1/2 top-1/2" :visible="loading" small />
                        </div>
                        <Button class="ml-2" outline>{{ __('sharp::form.geolocation.modal.search_button') }}</Button>
                    </div>
                </form>

                <template v-if="message">
                    <small>{{ message }}</small>
                </template>
            </div>
        </template>

        <component
            :is="components[field.mapsProvider.name]"
            class="max-w-full pb-[80%]"
            v-bind="{
                field,
                markerPosition,
                bounds,
                maxBounds: field.boundaries ? [field.boundaries.sw, field.boundaries.ne] : null,
                center: value ?? field.initialPosition ?? { lat:48.5838961, lng:7.7421826 },
                zoom: field.zoomLevel,
            } satisfies EditableMapComponentProps"
            @change="onMarkerPositionChange"
        />
    </div>
</template>

<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import { Loading, Modal } from "@/components/ui";
    import GeolocationEdit from './GeolocationEdit.vue';
    import { FormGeolocationFieldData } from "@/types";
    import type { Component } from "vue";
    import Gmaps from "./gmaps/Gmaps.vue";
    import Osm from "./osm/Osm.vue";
    import { onMounted, ref } from "vue";
    import { dd2dms, triggerResize } from "./utils";
    import { loadGmaps } from "./gmaps/load";
    import {
        Dialog,
        DialogClose,
        DialogFooter,
        DialogHeader,
        DialogScrollContent,
        DialogTitle
    } from "@/components/ui/dialog";
    import {
        EditableMapComponentProps,
        GeocodeParams, LatLng,
        MapComponentProps
    } from "@/form/components/fields/geolocation/types";
    import { Input } from "@/components/ui/input";
    import gmapsGeocode from "@/form/components/fields/geolocation/gmaps/geocode";
    import osmGeocode from "@/form/components/fields/geolocation/osm/geocode";

    const props = defineProps<{
        field: FormGeolocationFieldData,
        value: FormGeolocationFieldData['value'],
    }>();

    const emit = defineEmits(['input']);

    const components: Record<FormGeolocationFieldData['mapsProvider']['name'], Component> = {
        gmaps: Gmaps,
        osm: Osm,
    };

    const ready = ref(false);
    const modalOpen = ref(false);
    const modalLoading = ref(false);
    const modalMessage = ref('');
    const modalSearch = ref('');
    const modalMapBounds = ref(null);
    const modalMarkerPosition = ref(props.value);

    function geocode(params: GeocodeParams) {
        if(props.field.geocodingProvider.name === 'gmaps') {
            return gmapsGeocode(params);
        }
        return osmGeocode(params);
    }

    function onModalSearchSubmit() {
        const address = modalSearch.value;
        modalMessage.value = '';
        modalLoading.value = true;
        geocode({ address: modalSearch.value })
            .then(results => {
                if(results.length > 0) {
                    modalMapBounds.value = results[0].bounds;
                    modalMarkerPosition.value = results[0].location;
                } else {
                    modalMessage.value = __('sharp::form.geolocation.modal.geocode_input.message.no_results', { query: address ?? '' });
                }
            })
            .catch(status => {
                modalMessage.value = `${__(`sharp::form.geolocation.modal.geocode_input.message.error`)}${status?` (${status})`:''}`;
            })
            .finally(() => {
                modalLoading.value = false;
            });
    }

    function onModalMarkerPositionChange(position: LatLng) {
        if(props.field.geocoding) {
            modalLoading.value = true;
            geocode({ latLng: position })
                .then(results => {
                    if(results.length > 0) {
                        modalSearch.value = results[0].address;
                    }
                })
                .finally(() => {
                    modalLoading.value = false;
                });
        }
    }

    function onModalSubmit() {
        emit('input', modalMarkerPosition.value);
    }

    async function init() {
        if(props.field.mapsProvider.name === 'gmaps') {
            await loadGmaps(props.field.mapsProvider.options.apiKey);
        }
        ready.value = true;
    }

    init();

    onMounted(() => {
        new IntersectionObserver(entries => {
            if(entries[0].isIntersecting) {
                triggerResize();
            }
        });
    });
</script>

<template>
    <div>
        <template v-if="!ready">
            {{ __('sharp::form.geolocation.loading') }}
        </template>
        <template v-else-if="!value">
            <Button variant="secondary" class="w-full" @click="modalOpen = true">
                {{ __('sharp::form.geolocation.browse_button') }}
            </Button>
        </template>
        <template v-else>
            <div class="bg-background border border-input flex rounded-md p-4">
                <div class="w-[40%]">
                    <component
                        :is="components[field.mapsProvider.name]"
                        class="max-w-full pb-[80%]"
                        :marker-position="value"
                        :center="value"
                        :zoom="field.zoomLevel"
                        :max-bounds="field.boundaries ? [field.boundaries.sw, field.boundaries.ne] : null"
                        :tiles-url="field.mapsProvider.name === 'osm' ? field.mapsProvider.options.tilesUrl : null"
                    />
                </div>
                <div class="d-flex flex-column justify-content-between h-100">
                    <div>
                        <div><small>Latitude : {{ field.displayUnit === 'DMS' ? dd2dms(value.lat) : value.lat }}</small></div>
                        <div><small>Longitude : {{ field.displayUnit === 'DMS' ? dd2dms(value.lng, true) : value.lng }}</small></div>
                    </div>
                    <div>
                        <Button variant="destructive" size="sm" :disabled="field.readOnly"
                            @click="$emit('input', null)"
                        >
                            {{ __('sharp::form.geolocation.remove_button') }}
                        </Button>
                        <Button variant="outline" size="sm" :disabled="field.readOnly" @click="modalOpen = true">
                            {{ __('sharp::form.geolocation.edit_button') }}
                        </Button>
                    </div>
                </div>
            </div>
        </template>
        <Dialog
            v-model:open="modalOpen"
        >
            <DialogScrollContent class="gap-6" @pointer-down-outside.prevent>
                <DialogHeader>
                    <DialogTitle>
                        <template v-if="field.geocoding">
                            {{  __('sharp::form.geolocation.modal.title') }}
                        </template>
                        <template v-else>
                            {{__('sharp::form.geolocation.modal.title-no-geocoding') }}
                        </template>
                    </DialogTitle>
                </DialogHeader>

                <div>
                    <template v-if="field.geocoding">
                        <div class="mb-2">
                            <form @submit.prevent="onModalSearchSubmit">
                                <div class="flex">
                                    <Input class="flex-1" v-model="modalSearch"
                                        :placeholder="__('sharp::form.geolocation.modal.geocode_input.placeholder')"
                                    />
                                    <Button class="ml-2" variant="outline" :disabled="modalLoading">
                                        {{ __('sharp::form.geolocation.modal.search_button') }}
                                    </Button>
                                </div>
                            </form>

                            <template v-if="modalMessage">
                                <small>{{ modalMessage }}</small>
                            </template>
                        </div>
                    </template>

                    <component
                        :is="components[field.mapsProvider.name]"
                        class="aspect-[1/1]"
                        v-bind="{
                            field,
                            markerPosition: modalMarkerPosition,
                            bounds: modalMapBounds,
                            maxBounds: field.boundaries ? [field.boundaries.sw, field.boundaries.ne] : null,
                            center: value ?? field.initialPosition ?? { lat:48.5838961, lng:7.7421826 },
                            zoom: field.zoomLevel,
                            editable: true,
                        } satisfies MapComponentProps"
                        @change="onModalMarkerPositionChange"
                    />
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline">
                            {{ __('sharp::modals.cancel_button') }}
                        </Button>
                    </DialogClose>
                    <Button @click="onModalSubmit">
                        {{ __('sharp::modals.command.submit_button') }}
                    </Button>
                </DialogFooter>
            </DialogScrollContent>
        </Dialog>
    </div>
</template>

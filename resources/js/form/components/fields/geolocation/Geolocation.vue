<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Button } from '@/components/ui/button';
    import { FormGeolocationFieldData } from "@/types";
    import type { Component } from "vue";
    import Gmaps from "./gmaps/Gmaps.vue";
    import Osm from "./osm/Osm.vue";
    import { onMounted, ref } from "vue";
    import { dd2dms, triggerResize } from "./utils";
    import {
        Dialog,
        DialogClose,
        DialogFooter,
        DialogHeader,
        DialogScrollContent,
        DialogTitle
    } from "@/components/ui/dialog";
    import {
        GeocodeParams, LatLng,
        MapComponentProps
    } from "@/form/components/fields/geolocation/types";
    import { Input } from "@/components/ui/input";
    import gmapsGeocode from "@/form/components/fields/geolocation/gmaps/geocode";
    import osmGeocode from "@/form/components/fields/geolocation/osm/geocode";
    import { Alert } from "@/components/ui/alert";
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger
    } from "@/components/ui/dropdown-menu";
    import { MoreHorizontal, Search } from "lucide-vue-next";
    import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
    import { FormFieldEmits, FormFieldProps } from "@/form/types";

    const props = defineProps<FormFieldProps<FormGeolocationFieldData>>();
    const emit = defineEmits<FormFieldEmits<FormGeolocationFieldData>>();

    const components: Record<FormGeolocationFieldData['mapsProvider']['name'], Component> = {
        gmaps: Gmaps,
        osm: Osm,
    };

    const modalOpen = ref(false);
    const modalLoading = ref(false);
    const modalMessage = ref('');
    const modalSearch = ref('');
    const modalMapBounds = ref(null);
    const modalMarkerPosition = ref(props.value);

    async function geocode(params: GeocodeParams) {
        if(props.field.geocodingProvider.name === 'gmaps') {
            return gmapsGeocode(props.field.geocodingProvider.options.apiKey, params);
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
        modalMarkerPosition.value = position;
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
        modalOpen.value = false;
        emit('input', modalMarkerPosition.value);
    }

    function openModal() {
        modalOpen.value = true;
        modalMarkerPosition.value = props.value;
        if(props.field.geocoding && props.value) {
            modalLoading.value = true;
            geocode({ latLng: props.value })
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

    onMounted(() => {
        new IntersectionObserver(entries => {
            if(entries[0].isIntersecting) {
                triggerResize();
            }
        });
    });
</script>

<template>
    <FormFieldLayout field-group v-bind="props">
        <div class="@container">
            <template v-if="value">
                <div class="flex bg-background border border-input rounded-md gap-4 p-4">
                    <div class="flex-1 flex flex-col gap-4 @md:flex-row">
                        <div class="relative @md:w-[40%] h-40 rounded-md">
                            <Suspense>
                                <component
                                    :is="components[field.mapsProvider.name]"
                                    class="absolute inset-0 size-full rounded-md"
                                    v-bind="{
                                        field,
                                        markerPosition: value,
                                        maxBounds: field.boundaries ? [field.boundaries.sw, field.boundaries.ne] : null,
                                        center: value,
                                        zoom: field.zoomLevel,
                                    } satisfies MapComponentProps"
                                />
                            </Suspense>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs">Latitude : {{ field.displayUnit === 'DMS' ? dd2dms(value.lat) : value.lat }}</p>
                            <p class="mt-1 text-xs">Longitude : {{ field.displayUnit === 'DMS' ? dd2dms(value.lng, true) : value.lng }}</p>
                        </div>
                    </div>
                    <template v-if="!field.readOnly">
                        <DropdownMenu :modal="false">
                            <DropdownMenuTrigger as-child>
                                <Button class="self-center" variant="ghost" size="icon" :disabled="props.field.readOnly">
                                    <MoreHorizontal class="size-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent>
                                <DropdownMenuItem @click="openModal()">
                                    {{ __('sharp::form.geolocation.edit_button') }}
                                </DropdownMenuItem>
                                <DropdownMenuItem class="text-destructive" @click="$emit('input', null)">
                                    {{ __('sharp::form.geolocation.remove_button') }}
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </template>
                </div>
            </template>
            <template v-else>
                <Button variant="secondary" :disabled="props.field.readOnly" class="w-full" @click="openModal()">
                    {{ __('sharp::form.geolocation.browse_button') }}
                </Button>
            </template>
            <Dialog v-model:open="modalOpen">
                <DialogScrollContent class="gap-6 max-w-xl" @pointer-down-outside.prevent @open-auto-focus.prevent>
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
                            <form @submit.prevent="onModalSearchSubmit">
                                <div class="flex">
                                    <Input class="flex-1" v-model="modalSearch"
                                        :placeholder="__('sharp::form.geolocation.modal.geocode_input.placeholder')"
                                    />
                                    <Button class="ml-2" variant="secondary" :disabled="modalLoading">
                                        {{ __('sharp::form.geolocation.modal.search_button') }}
                                    </Button>
                                </div>
                            </form>

                            <template v-if="modalMessage">
                                <Alert class="mt-4 text-sm">
                                    <Search class="size-4" />
                                    <p>
                                        {{ modalMessage }}
                                    </p>
                                </Alert>
                            </template>
                        </template>

                        <Suspense>
                            <component
                                :is="components[field.mapsProvider.name]"
                                class="mt-4 rounded-md aspect-4/3"
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
                        </Suspense>
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
    </FormFieldLayout>
</template>

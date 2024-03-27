<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { Modal, Button } from "@/components/ui";

    import GeolocationEdit from './GeolocationEdit.vue';
    import { FormGeolocationFieldData } from "@/types";
    import type { Component } from "vue";
    import Gmaps from "./gmaps/Gmaps.vue";
    import Osm from "./osm/Osm.vue";
    import { onMounted, ref } from "vue";
    import { dd2dms, triggerResize } from "./utils";
    import { loadGmaps } from "./gmaps/load";

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
    const modalVisible = ref(false);
    const modalMarkerPosition = ref(props.value);

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
    <div class="SharpGeolocation">
        <template v-if="!ready">
            {{ __('sharp::form.geolocation.loading') }}
        </template>
        <template v-else-if="!value">
            <Button text block @click="modalVisible = true">
                {{ __('sharp::form.geolocation.browse_button') }}
            </Button>
        </template>
        <template v-else>
            <div class="card card-body form-control">
                <div class="row">
                    <div class="col-7">
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
                    <div class="col-5 pl-0">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <div><small>Latitude : {{ field.displayUnit === 'DMS' ? dd2dms(value.lat) : value.lat }}</small></div>
                                <div><small>Longitude : {{ field.displayUnit === 'DMS' ? dd2dms(value.lng, true) : value.lng }}</small></div>
                            </div>
                            <div>
                                <Button class="remove-button" variant="danger" small outline :disabled="field.readOnly"
                                    @click="$emit('input', null)"
                                >
                                    {{ __('sharp::form.geolocation.remove_button') }}
                                </Button>
                                <Button small outline :disabled="field.readOnly" @click="modalVisible = true">
                                    {{ __('sharp::form.geolocation.edit_button') }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <Modal
            v-model:visible="modalVisible"
            no-close-on-backdrop
            @ok="onModalSubmit"
        >
            <template #title>
                <template v-if="field.geocoding">
                    {{  __('sharp::form.geolocation.modal.title') }}
                </template>
                <template v-else>
                    {{__('sharp::form.geolocation.modal.title-no-geocoding') }}
                </template>
            </template>
            <GeolocationEdit
                :field="field"
                :value="value"
                v-model:marker-position="modalMarkerPosition"
            />
        </Modal>
    </div>
</template>

<template>
    <div class="SharpGeolocation">
        <template v-if="isLoading">
            {{ l('form.geolocation.loading') }}
        </template>
        <template v-else-if="isEmpty">
            <Button text block @click="handleShowModalButtonClicked">
                {{ l('form.geolocation.browse_button') }}
            </Button>
        </template>
        <template v-else>
            <div class="card card-body form-control">
                <div class="row">
                    <div class="col-7">
                        <component
                            :is="mapComponent"
                            class="SharpGeolocation__map"
                            :class="mapClasses"
                            :marker-position="value"
                            :center="value"
                            :zoom="zoomLevel"
                            :max-bounds="maxBounds"
                            :tiles-url="tilesUrl"
                        />
                    </div>
                    <div class="col-5 pl-0">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <div><small>Latitude : {{ latLngString.lat }}</small></div>
                                <div><small>Longitude : {{ latLngString.lng }}</small></div>
                            </div>
                            <div>
                                <Button class="remove-button" variant="danger" small outline :disabled="readOnly" @click="handleRemoveButtonClicked">
                                    {{ l('form.geolocation.remove_button') }}
                                </Button>
                                <Button small outline :disabled="readOnly" @click="handleEditButtonClicked">
                                    {{ l('form.geolocation.edit_button') }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <Modal
            :title="modalTitle"
            :visible.sync="modalVisible"
            no-close-on-backdrop
            @ok="handleModalSubmitted"
        >
            <transition :duration="300">
                <template v-if="modalVisible">
                    <GeolocationEdit
                        :location="value"
                        :center="value || initialPosition"
                        :zoom="zoomLevel"
                        :max-bounds="maxBounds"
                        :maps-provider="providerName(mapsProvider)"
                        :maps-options="providerOptions(mapsProvider)"
                        :geocoding="geocoding"
                        :geocoding-provider="providerName(geocodingProvider)"
                        :geocoding-options="providerOptions(geocodingProvider)"
                        @change="handleLocationChanged"
                    />
                </template>
            </transition>
        </Modal>
    </div>
</template>

<script>
    import { Modal, Button } from "sharp-ui";
    import { Localization } from 'sharp/mixins';

    import { getMapByProvider, loadMapProvider } from "./maps";
    import { dd2dms, tilesUrl, providerName, providerOptions, triggerResize } from "./util";

    import GeolocationEdit from './GeolocationEdit.vue';


    export default {
        name: 'SharpGeolocation',
        mixins: [Localization],

        inject: {
            $tab: {
                default: null
            }
        },

        components: {
            GeolocationEdit,
            Button,
            Modal,
        },

        props: {
            value: Object,
            readOnly: Boolean,
            uniqueIdentifier: String,
            geocoding: Boolean,
            apiKey: String,
            boundaries: Object,
            zoomLevel: {
                type: Number,
                default: 4
            },
            initialPosition: {
                type: Object,
                default: () => ({
                    lat: 46.1445458,
                    lng: -2.4343779
                })
            },
            displayUnit: {
                type: String,
                default: 'DD',
                validator: unit => unit==='DMS' || unit==='DD'
            },
            mapsProvider: {
                type: Object,
                default: ()=>({
                    name: 'gmaps',
                }),
            },
            geocodingProvider: {
                type: Object,
                default:() => ({
                    name: 'gmaps',
                }),
            },
        },
        data() {
            return {
                ready: false,
                modalVisible: false,
                location: this.value,
            }
        },
        computed: {
            isLoading() {
                return !this.ready;
            },
            isEmpty() {
                return !this.value;
            },
            latLngString() {
                if(this.displayUnit === 'DMS') {
                    return {
                        lat: dd2dms(this.value.lat),
                        lng: dd2dms(this.value.lng, true)
                    }
                } else if(this.displayUnit === 'DD') {
                    return this.value;
                }
            },
            mapComponent() {
                return getMapByProvider(providerName(this.mapsProvider));
            },
            mapClasses() {
                return [
                    `SharpGeolocation__map--${providerName(this.mapsProvider)}`,
                ];
            },
            tilesUrl() {
                const mapsOptions = providerOptions(this.mapsProvider);
                return tilesUrl(mapsOptions);
            },
            maxBounds() {
                return this.boundaries
                    ? [this.boundaries.sw, this.boundaries.ne]
                    : null;
            },
            modalTitle() {
                return this.geocoding
                    ? this.l('form.geolocation.modal.title')
                    : this.l('form.geolocation.modal.title-no-geocoding');
            },
        },
        methods: {
            providerName,
            providerOptions,

            handleModalSubmitted() {
                this.$emit('input', this.location);
            },
            handleRemoveButtonClicked() {
                this.$emit('input', null);
            },
            handleShowModalButtonClicked() {
                this.modalVisible = true;
            },
            handleEditButtonClicked() {
                this.modalVisible = true;
            },
            handleLocationChanged(location) {
                this.location = location;
            },
            loadProvider(providerData) {
                const name = providerName(providerData);
                const { apiKey } = providerOptions(providerData);
                return loadMapProvider(name, {
                    apiKey,
                });
            },
            async init() {
                await this.loadProvider(this.mapsProvider);
                if(this.geocodingProvider) {
                    await this.loadProvider(this.geocodingProvider);
                }
                this.ready = true;
            }
        },
        created() {
            this.init();
        },
        mounted() {
            this.$tab?.$once('active', () => {
                // force update maps components on tab active
                triggerResize();
            });
        }
    }
</script>

<template>
    <div class="SharpGeolocation">
        <template v-if="isLoading">
            {{ l('form.geolocation.loading') }}
        </template>
        <template v-else-if="isEmpty">
            <SharpButton outline class="w-100" @click="handleShowModalButtonClicked">
                {{ l('form.geolocation.browse_button') }}
            </SharpButton>
        </template>
        <template v-else>
            <SharpCard light class="SharpModule--closeable"
                :has-close="!readOnly"
                @close-click="handleRemoveButtonClicked"
            >
                <div class="row">
                    <div class="col-7">
                        <component
                            :is="mapComponent"
                            :marker-position="value"
                            :center="value || initialPosition"
                            :zoom="zoomLevel"
                        />
                    </div>
                    <div class="col-5 pl-0">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <div><small>Latitude : {{ latLngString.lat }}</small></div>
                                <div><small>Longitude : {{ latLngString.lng }}</small></div>
                            </div>
                            <div>
                                <SharpButton small outline type="danger" class="remove-button" :disabled="readOnly" @click="handleRemoveButtonClicked">
                                    {{ l('form.geolocation.remove_button') }}
                                </SharpButton>
                                <SharpButton small outline :disabled="readOnly" @click="handleEditButtonClicked">
                                    {{ l('form.geolocation.edit_button') }}
                                </SharpButton>
                            </div>
                        </div>
                    </div>
                </div>
            </SharpCard>
        </template>
        <SharpModal
            :title="modalTitle"
            :visible.sync="modalVisible"
            no-close-on-backdrop
            @ok="handleModalSubmitted"
        >
            <transition :duration="300">
                <template v-if="modalVisible">
                    <SharpGeolocationEdit
                        :location="value"
                        :center="value || initialPosition"
                        :zoom="zoomLevel"
                        :maps-provider="mapsProvider"
                        :geocoding="geocoding"
                        :geocoding-provider="geocodingProvider"
                        @change="handleLocationChanged"
                    />
                </template>
            </transition>
        </SharpModal>
    </div>
</template>

<script>
    import { Localization } from '../../../../mixins';
    import { SharpCard, SharpButton } from "../../../ui";
    import SharpModal from '../../../Modal';

    import { getMapByProvider, loadMapProvider } from "./maps";
    import { dd2dms } from "./util";

    import SharpGeolocationEdit from './GeolocationEdit.vue';


    export default {
        name: 'SharpGeolocation',
        mixins: [Localization],

        components: {
            SharpGeolocationEdit,
            SharpCard,
            SharpButton,
            SharpModal,
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
                type: String,
                default: 'gmaps',
            },
            geocodingProvider: {
                type: String,
                default: 'gmaps',
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
                }
                else if(this.displayUnit === 'DD') {
                    return this.value;
                }
            },
            mapComponent() {
                return getMapByProvider(this.mapsProvider);
            },
            modalTitle() {
                return this.geocoding
                    ? this.l('form.geolocation.modal.title')
                    : this.l('form.geolocation.modal.title-no-geocoding');
            },
        },
        methods: {
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
            async init() {
                await loadMapProvider(this.mapsProvider, {
                    apiKey: this.apiKey
                });
                this.ready = true;
            }
        },
        created() {
            this.init();
        },
    }
</script>

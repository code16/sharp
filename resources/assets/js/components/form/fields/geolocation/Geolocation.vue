<template>
    <div class="SharpGeolocation">
        <template v-if="!loaded">
            {{ l('form.geolocation.loading') }}
        </template>
        <template v-else-if="!value">
            <SharpButton outline class="w-100" v-b-modal="modalId">
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
                        <GmapMap
                            :center="value"
                            :zoom="zoomLevel"
                            :options="defaultMapOptions"
                            style="padding-bottom: 80%"
                            class="mw-100"
                            ref="map"
                        >
                            <GmapMarker :position="value"></GmapMarker>
                        </GmapMap>
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
                                <SharpButton small outline :disabled="readOnly" v-b-modal="modalId">
                                    {{ l('form.geolocation.edit_button') }}
                                </SharpButton>
                            </div>
                        </div>
                    </div>
                </div>
            </SharpCard>
        </template>
        <SharpGeolocationEdit
            :modal-id="modalId"
            :value="value"
            :center="value || initialPosition"
            :zoom="zoomLevel"
            :geocoding="geocoding"
            @change="handlePositionChanged"
        />
    </div>
</template>

<script>
    import { Map, Marker, loadGmapApi } from 'vue2-google-maps';
    import bModal from 'bootstrap-vue/es/directives/modal/modal';

    import { Localization } from '../../../../mixins';

    import { SharpCard, SharpButton } from '../../../ui';
    import SharpGeolocationEdit from './GeolocationEdit.vue';
    import GeolocationCommons from './Commons';

    export default {
        name: 'SharpGeolocation',
        mixins: [Localization, GeolocationCommons],

        inject: ['$tab'],

        components: {
            GmapMap: Map,
            GmapMarker: Marker,
            SharpGeolocationEdit,
            SharpCard,
            SharpButton
        },

        props: {
            value: Object,
            readOnly: Boolean,
            uniqueIdentifier: String,
            geocoding: Boolean,
            apiKey: String,
            boundaries: Object,
            zoomLevel: Number,
            initialPosition: Object,
            displayUnit: {
                type: String,
                default: 'DD',
                validator: unit => unit==='DMS'||unit==='DD'
            }
        },

        data() {
            return {
                loaded: false
            }
        },

        computed: {
            modalId() {
                return `${this.uniqueIdentifier.replace('.','-')}-modal`
            },
            latLngString() {
                if(this.displayUnit === 'DMS') {
                    return this.latLng2DMS(this.value)
                }
                else if(this.displayUnit === 'DD') {
                    return this.latLng2DD(this.value);
                }
            }
        },

        methods:{
            handlePositionChanged(value) {
                this.$emit('input', value);
            },
            handleRemoveButtonClicked() {
                this.$emit('input', null);
            },

            load() {
                if(!this.$root.$_gmapInit) {
                    let loadOptions = { v:'3' };
                    if(this.apiKey) loadOptions.key = this.apiKey;

                    loadGmapApi(loadOptions);
                    this.$root.$_gmapInit = true;
                }
                return this.$gmapApiPromiseLazy();
            }
        },

        directives: {
            bModal
        },

        async created() {
            await this.load();
            this.loaded = true;
        },

        mounted() {
            if(this.$tab) {
                this.$tab.$on('active', ()=>this.refreshMap());
            }
        }
    }
</script>
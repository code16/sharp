<template>
    <div class="SharpGeolocation">
        <template v-if="!value">
            <SharpButton outline class="w-100" v-b-modal="modalId">
                {{ l('form.geolocation.browse_button') }}
            </SharpButton>
        </template>
        <template v-else>
            <SharpCard>
                <div class="row">
                    <div class="col-8">
                        <GmapMap
                            :center="value"
                            :zoom="4"
                            :options="defaultMapOptions"
                            style="padding-bottom: 80%"
                            class="mw-100"
                            ref="map"
                        >
                            <GmapMarker :position="value"></GmapMarker>
                        </GmapMap>
                    </div>
                    <div class="col-4">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <div><small>Latitude : {{ latLngString.lat }}</small></div>
                                <div><small>Longitude : {{ latLngString.lng }}</small></div>
                            </div>
                            <div>
                                <SharpButton small outline type="danger" @click="handleRemoveButtonClicked"> {{ l('form.geolocation.remove_button') }}</SharpButton>
                                <SharpButton small outline v-b-modal="modalId"> {{ l('form.geolocation.edit_button') }} </SharpButton>
                            </div>
                        </div>
                    </div>
                </div>
            </SharpCard>
        </template>
        <SharpGeolocationEdit
            :modal-id="modalId"
            :value="value"
            :center="initialPosition"
            @change="handlePositionChanged"
        />
    </div>
</template>

<script>
    import Vue from 'vue';
    import { install as VueGoogleMaps, Map, Marker } from 'vue2-google-maps';

    import { Localization } from '../../../../mixins';

    import { SharpCard, SharpButton } from '../../../ui';
    import SharpGeolocationEdit from './Edit.vue';
    import GeolocationCommons from './Commons';

    import { error } from '../../../../util';

    Vue.use(VueGoogleMaps, {
        installComponents: false,
        load: {
            v: '3'
        }
    });


    export default {
        name: 'SharpGeolocation',
        mixins: [Localization, GeolocationCommons],

        components: {
            GmapMap: Map,
            GmapMarker: Marker,
            SharpGeolocationEdit,
            SharpCard,
            SharpButton
        },

        props: {
            value: Object,
            uniqueIdentifier: String,
            geocoding: Boolean,
            apiKey: String,
            boundaries: Object,
            zoomLevel: Number,
            initialPosition: Object,
            displayUnit: {
                type: String,
                default: 'DD'
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
                    return this.value;
                }
                error(`Geolocation, unknown displayUnit : '${this.displayUnit}'`);
            }
        },

        methods:{
            handlePositionChanged(value) {
                this.$emit('input', value);
            },
            handleRemoveButtonClicked() {
                this.$emit('input', null);
            }
        },

        mounted() {
            console.log(this.$refs.map);
        }
    }
</script>
<template>
    <SharpModal
        :id="modalId"
        :title="lSub('title')"
        no-close-on-backdrop
        @show="handleModalOpened"
        @hidden="handleModalClosed"
        @ok="handleModalOk"
    >
        <template v-if="opened">
            <div class="mb-2">
                <div class="position-relative">
                    <SharpText :placeholder="lSub('geocode_input.placeholder')" @keyup.native.enter="handleGeocodeChanged"/>
                    <SharpLoading
                        v-show="loading" small inline visible
                        class="position-absolute m-auto"
                        style="top:0;right:0;bottom:0">
                    </SharpLoading>
                </div>
                <div v-if="message"><small>{{ message }}</small></div>
            </div>
            <GmapMap
                class="mw-100"
                style="padding-bottom: 80%;"
                :center="center"
                :zoom="zoom"
                :options="defaultMapOptions"
                @click="handleMapClicked"
                ref="map"
            >
                <GmapMarker
                    v-if="position"
                    :position="position"
                    draggable
                    @dragend="handleMarkerDragged"
                >
                </GmapMarker>
            </GmapMap>
        </template>
    </SharpModal>
</template>

<script>
    import Text from '../Text.vue';
    import { Map, Marker } from 'vue2-google-maps';
    import GeolocationCommons from './Commons';
    import Modal from '../../../Modal.vue';
    import FieldContainer from '../../FieldContainer';
    import { SharpLoading } from "../../../ui";
    import { LocalizationBase } from '../../../../mixins';

    export default {
        name:'SharpGeolocationEdit',

        mixins: [ LocalizationBase('form.geolocation.modal'), GeolocationCommons],

        components: {
            [Text.name]: Text,
            [Modal.name]: Modal,
            [FieldContainer.name]:FieldContainer,
            GmapMap: Map,
            GmapMarker: Marker,
            SharpLoading
        },

        props: {
            value: Object,
            center: {
                type: Object,
                default: ()=>({
                    lat: 46.1445458,
                    lng: -2.4343779
                })
            },
            zoom: {
                type: Number,
                default:4
            },
            modalId: String
        },

        data() {
            return {
                position: this.value,
                opened: false,
                loading: false,
                status: null
            }
        },

        watch: {
            value:'updatePosition'
        },

        computed: {
            geocoder() {
                return new google.maps.Geocoder();
            },
            message() {
                return status !== 'OK' && this.lSub(`geocode_input.message.${this.status}`);
            }
        },

        methods: {
            handleModalOpened() {
                this.opened = true;
            },
            handleModalClosed() {
                this.opened = false;

                // resetting values
                this.position = this.value;
                this.loading = false;
                this.status = null;
            },
            handleModalOk(e) {
                this.$emit('change', this.position);
            },
            handleMapClicked(e) {
                this.updatePosition(e.latLng);
            },
            handleMarkerDragged(e) {
                console.log(e);
            },
            async handleGeocodeChanged(e) {
                this.loading = true;
                try {
                    let location = await this.geocode(e.target.value);
                    this.updatePosition(location, { pan:true });
                }
                catch(e) { }
                finally { this.loading = false; }
            },
            async geocode(address) {
                return new Promise((resolve, reject)=>{
                    this.geocoder.geocode({ address }, (results, status) => {
                        this.status = status;
                        if(status === 'OK') resolve(results[0].geometry.location);
                        else reject(status);
                    })
                });
            },
            updatePosition(latLng, { pan }={}) {
                this.status = null;
                this.position = latLng;
                pan && this.$refs.map.$mapObject.panTo(latLng);
            },
        }
    }
</script>
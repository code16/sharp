<template>
    <SharpModal
        :id="modalId"
        :title="lSub(geocoding ? 'title' : 'title-no-geocoding')"
        no-close-on-backdrop
        @show="handleModalOpened"
        @hidden="handleModalClosed"
        @ok="handleModalOk"
    >
        <div v-if="geocoding" class="mb-2">
            <div class="position-relative">
                <SharpText v-model="search" :placeholder="lSub('geocode_input.placeholder')" @keyup.native.enter="handleGeocodeChanged"/>
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
            >
            </GmapMarker>
        </GmapMap>
    </SharpModal>
</template>

<script>
    import Text from '../Text.vue';
    import { Map, Marker } from '../../../vendor/vue2-google-maps/main';
    import GeolocationCommons from './Commons';
    import Modal from '../../../Modal.vue';
    import { SharpLoading } from "../../../ui";
    import { LocalizationBase } from '../../../../mixins';

    export default {
        name:'SharpGeolocationEdit',

        mixins: [ LocalizationBase('form.geolocation.modal'), GeolocationCommons],

        components: {
            [Text.name]: Text,
            [Modal.name]: Modal,
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
            geocoding: Boolean,
            modalId: String
        },

        data() {
            return {
                position: this.value,
                opened: false,
                loading: false,
                search: '',
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
                let msg = this.lSub(`geocode_input.message.${this.status}`);
                switch(this.status) {
                    case 'ZERO_RESULTS': return msg.replace('(...)', `'${this.search}'`);
                }
                return msg;
            }
        },

        methods: {
            reset() {
                Object.assign(this, this.$options.data.call(this));
                let { $mapObject } = this.$refs.map;
                $mapObject.setCenter(this.center);
                $mapObject.setZoom(this.zoom);
            },
            handleModalOpened() {
                this.opened = true;
                this.refreshMap();
            },
            handleModalClosed() {
                this.opened = false;
                this.reset();
            },
            handleModalOk() {
                this.$emit('change', this.position);
            },
            handleMapClicked(e) {
                this.updatePosition(e.latLng);
            },
            async handleGeocodeChanged(e) {
                this.loading = true;
                try {
                    let geo = await this.geocode(e.target.value);
                    this.updatePosition(geo.location);
                    this.move(geo);
                }
                catch(e) { console.log(e); }
                finally { this.loading = false; }
            },
            async geocode(address) {
                return new Promise((resolve, reject)=>{
                    this.geocoder.geocode({ address }, (results, status) => {
                        this.status = status;
                        if(status === 'OK') resolve(results[0].geometry);
                        else reject(status);
                    })
                });
            },
            updatePosition(latLng) {
                this.position = latLng;
            },
            move(geometry) {
                let { $mapObject } = this.$refs.map;
                $mapObject.setCenter(geometry.location);
                $mapObject.fitBounds(geometry.viewport);
            }
        }
    }
</script>
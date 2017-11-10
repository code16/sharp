<template>
    <SharpModal :id="modalId" @hide="handleModalClosed">
        <!-- <SharpAutoComplete /> -->
        <SharpText />
        <GmapMap
            :center="center"
            :zoom="4"
            :options="defaultMapOptions"
            style="width: 500px; height: 300px"
            class="mw-100"
            ref="map"
        >
            <GmapMarker v-if="position" :position="position" draggable></GmapMarker>
        </GmapMap>
    </SharpModal>
</template>

<script>
    import Text from '../Text.vue';
    import { Map, Marker } from 'vue2-google-maps';
    import GeolocationCommons from './Commons';
    import Modal from '../../../Modal.vue';

    export default {
        name:'SharpGeolocationEdit',

        mixins: [GeolocationCommons],

        components: {
            [Text.name]: Text,
            [Modal.name]: Modal,
            GmapMap: Map,
            GmapMarker: Marker
        },

        props: {
            value: Object,
            center: Object,
            modalId: String
        },

        data() {
            return {
                position: this.value
            }
        },

        watch: {
            value(value) {
                this.position = value;
            }
        },

        methods: {
            localize() {
                navigator.geolocation.getCurrentPosition(({ coords }) => {
                    let pos = {
                        lat: coords.latitude,
                        lng: coords.longitude
                    };
                    this.$refs.map.$mapObject.setCenter(pos);
                }, e => {
                    console.log("can't locate", e);
                });
            },
            handleModalClosed(e) {
                this.$emit('change', this.position);
            }
        },
        mounted() {

        }
    }
</script>
<template>
    <SharpModal :id="modalId" @hide="handleModalClosed">
        <!-- <SharpAutoComplete /> -->

        <SharpText class="mb-2"/>
        <GmapMap
            class="mw-100"
            style="padding-bottom: 80%;"
            :center="center"
            :zoom="4"
            :options="defaultMapOptions"
            @click="handleMapClicked"
            ref="map"
        >
            <GmapMarker v-if="position" :position="position" draggable @dragend="handleMarkerDragged"></GmapMarker>
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
            center: {
                type: Object,
                default: ()=>({
                    lat: 46.1445458,
                    lng: -2.4343779
                })
            },
            modalId: String
        },

        data() {
            return {
                position: this.value
            }
        },

        watch: {
            value:'updatePosition'
        },

        methods: {
            handleModalClosed(e) {
                this.$emit('change', this.position);
            },
            handleMapClicked(e) {
                this.updatePosition(e.latLng);
            },
            handleMarkerDragged(e) {
                console.log(e);
            },
            updatePosition(latLng) {
                this.position = latLng;
            }
        },
        mounted() {

        }
    }
</script>
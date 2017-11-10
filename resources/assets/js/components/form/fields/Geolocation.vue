<template>
    <div class="SharpGeolocation">
        <template v-if="!value">
            <button class="SharpButton SharpButton--secondary" v-b-modal="modalId">
                {{ l('form.geolocation.browse_button') }}
            </button>
        </template>
        <template v-else>
            <gmap-map :center="value" :zoom="10" style="width: 500px; height: 300px"></gmap-map>
        </template>
        <sharp-modal :id="modalId">
            <gmap-map :center="{ lat:0, lng:0 }" :zoom="2" style="width: 500px; height: 300px"></gmap-map>
        </sharp-modal>
    </div>
</template>

<script>
    import Vue from 'vue';
    import * as VueGoogleMaps from 'vue2-google-maps';

    import Modal from '../../Modal.vue';
    import { Localization } from '../../../mixins';

    Vue.use(VueGoogleMaps, {
        installComponents: false,
        load: {
            v: '3.26'
        }
    });

    export default {
        name: 'SharpGeolocation',
        mixins:[Localization],
        components: {
            GmapMap:VueGoogleMaps.Map,
            GmapMarker:VueGoogleMaps.Marker,
            [Modal.name]:Modal
        },
        props: {
            value: Object,
            uniqueIdentifier: String,
            geocoding: Boolean,
            apiKey: String,
            boundaries: Object,
            displayUnit: {
                type: String,
                default: 'DD'
            }
        },
        computed: {
            modalId() {
                return `${this.uniqueIdentifier.replace('.','-')}-modal`
            }
        }
    }
</script>
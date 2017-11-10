<template>
    <div class="SharpGeolocation">
        <template v-if="!value">
            <button class="SharpButton SharpButton--secondary w-100" v-b-modal="modalId">
                {{ l('form.geolocation.browse_button') }}
            </button>
        </template>
        <template v-else>
            <GmapMap
                :center="value"
                :zoom="4"
                :options="defaultMapOptions"
                style="height: 300px; width: 500px"
                class="mw-100"
                ref="map"
            >
                <GmapMarker :position="center"></GmapMarker>
            </GmapMap>
        </template>
        <SharpGeolocationEdit :modal-id="modalId" :value="value" :center="initialPosition" @change="handlePositionChanged" />
    </div>
</template>

<script>
    import Vue from 'vue';
    import { install as VueGoogleMaps, Map, Marker } from 'vue2-google-maps';

    import { Localization } from '../../../../mixins/index';

    import Edit from './Edit.vue';
    import GeolocationCommons from './Commons';

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
            [Edit.name]:Edit
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
        },

        methods:{
            handlePositionChanged(value) {
                this.$emit('input', value);
            }
        },

        mounted() {
            console.log(this.$refs.map);
        }
    }
</script>
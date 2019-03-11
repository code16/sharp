<template>
    <SharpGeolocation :value="value" :loaded="loaded">
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
        <template slot="modal">
            <SharpGmapsGeolocationEdit
                :modal-id="modalId"
                :value="value"
                :center="value || initialPosition"
                :zoom="zoomLevel"
                :geocoding="geocoding"
                @change="handlePositionChanged"
            />
        </template>
    </SharpGeolocation>
</template>

<script>
    import { Map, Marker, loadGmapApi } from 'vue2-google-maps';
    import SharpGmapsGeolocationEdit from './GmapsGeolocationEdit.vue';
    import GeolocationCommons from './commons';
    import SharpGeolocation from '../Geolocation.vue';

    export default {
        name: 'SharpGmapsGeolocation',
        mixins: [GeolocationCommons],

        inject: ['$tab'],

        components: {
            GmapMap: Map,
            GmapMarker: Marker,
            SharpGmapsGeolocationEdit,
            SharpGeolocation
        },

        props: {
            value: Object,
            readOnly: Boolean,
            uniqueIdentifier: String,
            geocoding: Boolean,
            apiKey: String,
            boundaries: Object,
            zoomLevel: Number,
            initialPosition: Object
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
        },

        methods:{
            handlePositionChanged(value) {
                this.$emit('input', value.toJson());
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

        async created() {
            await this.load();
            this.loaded = true;
        },

        mounted() {
            if(this.$tab) {
                this.$tab.$on('active', ()=>this.refreshMap());
            }
        },
    }
</script>
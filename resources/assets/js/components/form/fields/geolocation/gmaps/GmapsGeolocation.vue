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
    import Vue from 'vue';
    import * as VueGoogleMaps from '../../../../vendor/vue2-google-maps/main';
    
    import SharpGmapsGeolocationEdit from './GmapsGeolocationEdit.vue';
    import GeolocationCommons from './commons';
    import SharpGeolocation from '../Geolocation.vue';

    export default {
        name: 'SharpGmapsGeolocation',
        mixins: [GeolocationCommons],

        inject: ['$tab'],

        components: {
            GmapMap: VueGoogleMaps.Map,
            GmapMarker: VueGoogleMaps.Marker,
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

            async load() {
                if(this.$root.$_gmapLoaded) {
                    return this.$root.$_gmapLoaded;
                }

                let loadOptions = { v:'3' };
                if(this.apiKey) loadOptions.key = this.apiKey;

                Vue.use(VueGoogleMaps, {
                    installComponents: false,
                    load: loadOptions
                });

                this.$root.$_gmapLoaded = VueGoogleMaps.loaded;
                return VueGoogleMaps.loaded;
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
        }
    }
</script>
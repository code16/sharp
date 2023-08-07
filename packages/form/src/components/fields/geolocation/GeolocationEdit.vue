<script setup lang="ts">
    import { __ } from "@/utils/i18n";
</script>

<template>
    <div class="SharpGeolocationEdit" :class="classes">
        <template v-if="hasGeocoding">
            <div class="mb-2">
                <form @submit.prevent="handleSearchSubmitted">
                    <div class="row no-gutters">
                        <div class="col position-relative">
                            <TextField
                                class="SharpGeolocationEdit__input"
                                :value="search"
                                :placeholder="__('sharp::form.geolocation.modal.geocode_input.placeholder')"
                                @input="handleSearchInput"
                            />
                            <Loading class="SharpGeolocationEdit__loading" :visible="loading" small />
                        </div>
                        <div class="col-auto pl-2">
                            <Button outline>{{ __('sharp::form.geolocation.modal.search_button') }}</Button>
                        </div>
                    </div>
                </form>

                <template v-if="message">
                    <small>{{ message }}</small>
                </template>
            </div>
        </template>

        <component
            :is="editableMapComponent"
            class="SharpGeolocationEdit__map"
            :class="mapClasses"
            :marker-position="currentLocation"
            :center="center"
            :bounds="currentBounds"
            :zoom="zoom"
            :max-bounds="maxBounds"
            :tiles-url="tilesUrl"
            @change="handleMarkerPositionChanged"
        />
    </div>
</template>

<script lang="ts">
    import { Loading, Button, Modal } from '@sharp/ui';
    import TextField from '../Text.vue';
    import { getEditableMapByProvider, geocode } from "./maps";
    import { tilesUrl } from "./util";
    import { __ } from "@/utils/i18n";

    export default {
        components: {
            Loading,
            Modal,
            TextField,
            Button,
        },
        props: {
            location: Object,
            center: Object,
            bounds: Object,
            zoom: Number,
            maxBounds: Array,
            geocoding: Boolean,
            mapsProvider: {
                type: String,
                default: 'gmaps',
            },
            mapsOptions: Object,
            geocodingProvider: {
                type: String,
                default: 'gmaps',
            },
            geocodingOptions: Object,
        },
        data() {
            return {
                loading: false,
                search: null,
                message: null,

                currentLocation: this.location,
                currentBounds: this.bounds,
            }
        },
        computed: {
            editableMapComponent() {
                return getEditableMapByProvider(this.mapsProvider);
            },
            hasGeocoding() {
                return this.geocoding;
            },
            classes() {
                return {
                    'SharpGeolocationEdit--loading': this.loading,
                }
            },
            mapClasses() {
                return [
                    `SharpGeolocationEdit__map--${this.mapsProvider}`,
                ]
            },
            tilesUrl() {
                return tilesUrl(this.mapsOptions);
            },
        },
        methods: {
            handleSearchInput(search) {
                this.search = search;
            },
            handleMarkerPositionChanged(position) {
                this.currentLocation = position;
                this.message = '';
                this.$emit('change', this.currentLocation);
                if(this.hasGeocoding) {
                    this.loading = true;
                    geocode(this.geocodingProvider, { latLng:position }, this.geocodingOptions)
                        .then(results => {
                            if(results.length > 0) {
                                this.search = results[0].address;
                            }
                        })
                        .finally(()=>{
                            this.loading = false;
                        });
                }
            },
            handleSearchSubmitted() {
                const address = this.search;
                this.message = '';
                this.loading = true;
                geocode(this.geocodingProvider, { address }, this.geocodingOptions)
                    .then(results => {
                        if(results.length > 0) {
                            this.currentLocation = results[0].location;
                            this.currentBounds = results[0].bounds;
                            this.$emit('change', this.currentLocation);
                        } else {
                            this.message = __('sharp::form.geolocation.modal.geocode_input.message.no_results', { query: address ?? '' });
                        }
                    })
                    .catch(status => {
                        this.message = `${__(`sharp::form.geolocation.modal.geocode_input.message.error`)}${status?` (${status})`:''}`;
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
        },
    }
</script>

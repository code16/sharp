<template>
    <div class="SharpGeolocationEdit" :class="classes">
        <template v-if="hasGeocoding">
            <div class="mb-2">
                <form @submit.prevent="handleSearchSubmitted">
                    <div class="row no-gutters">
                        <div class="col position-relative">
                            <SharpText :value="search" class="SharpGeolocationEdit__input" :placeholder="lSub('geocode_input.placeholder')" @input="handleSearchInput" />
                            <template v-if="loading">
                                <SharpLoading visible small inline
                                    class="SharpGeolocationEdit__loading"
                                />
                            </template>
                        </div>
                        <div class="col-auto pl-2">
                            <SharpButton outline>Rechercher</SharpButton>
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
            :marker-position="currentLocation"
            :center="center"
            :bounds="currentBounds"
            :zoom="zoom"
            @map-click="handleMapClicked"
        />
    </div>
</template>

<script>
    import { SharpLoading, SharpButton } from "../../../ui";
    import SharpModal from '../../../Modal';
    import SharpText from '../Text';
    import { LocalizationBase } from '../../../../mixins';
    import { getEditableMapByProvider, geocode } from "./maps";

    export default {
        mixins: [LocalizationBase('form.geolocation.modal')],
        components: {
            SharpLoading,
            SharpModal,
            SharpText,
            SharpButton,
        },
        props: {
            location: Object,
            center: Object,
            bounds: Object,
            zoom: Number,
            geocoding: Boolean,
            mapsProvider: {
                type: String,
                default: 'gmaps',
            },
            geocodingProvider: {
                type: String,
                default: 'gmaps',
            },
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
        },
        methods: {
            handleSearchInput(search) {
                this.search = search;
            },
            handleMapClicked(position) {
                this.currentLocation = position;
                this.message = '';
                this.$emit('change', this.currentLocation);
                if(this.hasGeocoding) {
                    this.loading = true;
                    geocode(this.geocodingProvider, { latLng:position })
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
                geocode(this.geocodingProvider, { address })
                    .then(results => {
                        if(results.length > 0) {
                            this.currentLocation = results[0].location;
                            this.currentBounds = results[0].bounds;
                            this.$emit('change', this.currentLocation);
                        } else {
                            this.message = this.lSub(`geocode_input.message.no_results`).replace(':query', address || '');
                        }
                    })
                    .catch(status => {
                        this.message = `${this.lSub(`geocode_input.message.error`)}${status?` (${status})`:''}`;
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
        },
    }

</script>
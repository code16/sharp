<template>
    <SharpModal
        class="SharpGeolocationModal"
        :class="classes"
        :title="lSub(geocoding ? 'title' : 'title-no-geocoding')"
        :visible="visible"
        no-close-on-backdrop
        @show="handleShow"
        @change="handleVisibilityChanged"
        @ok="handleOkButtonClicked"
    >
        <template v-if="hasGeocoding">
            <div class="mb-2">
                <form @submit.prevent="handleSearchSubmitted">
                    <div class="row no-gutters">
                        <div class="col position-relative">
                            <SharpText :value="search" class="SharpGeolocationModal__input" :placeholder="lSub('geocode_input.placeholder')" @input="handleSearchInput" />
                            <template v-if="loading">
                                <SharpLoading visible small inline
                                    class="SharpGeolocationModal__loading"
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

        <template v-if="visible">
            <component
                :is="editableMapComponent"
                :marker-position="currentLocation"
                :center="resolvedCenter"
                :bounds="resolvedBounds"
                :zoom="zoom"
                @map-click="handleMapClicked"
            />
        </template>
    </SharpModal>
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
            visible: Boolean,
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

                currentLocation: null,
                currentBounds: null,
            }
        },
        computed: {
            editableMapComponent() {
                return getEditableMapByProvider(this.mapsProvider);
            },
            resolvedCenter() {
                return this.$props.center;
            },
            resolvedBounds() {
                return this.currentBounds;
            },
            hasGeocoding() {
                return this.geocoding;
            },
            classes() {
                return {
                    'SharpGeolocationModal--loading': this.loading,
                }
            },
        },
        methods: {
            handleShow() {
                this.currentLocation = this.location;
                this.currentBounds = this.bounds;
                this.search = null;
                this.message = null;
            },
            handleVisibilityChanged(visible) {
                this.$emit('update:visible', visible);
            },
            handleOkButtonClicked() {
                this.$emit('submit', this.currentLocation);
            },
            handleSearchInput(search) {
                this.search = search;
            },
            handleMapClicked(position) {
                this.currentLocation = position;
                this.message = '';
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
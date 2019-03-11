<template>
    <SharpModal
        :title="lSub(geocoding ? 'title' : 'title-no-geocoding')"
        no-close-on-backdrop
        :visible="visible"
        @change="handleVisibilityChanged"
        @ok="handleOkButtonClicked"
    >
        <div v-if="geocoding" class="mb-2">
            <div class="position-relative">
                <form @submit.prevent="handleSearchSubmitted">
                    <SharpText :value="search" :placeholder="lSub('geocode_input.placeholder')" @input="handleSearchInput" />
                </form>
                <template v-if="loading">
                    <SharpLoading
                        small inline visible
                        class="position-absolute m-auto"
                        style="top:0;right:0;bottom:0"
                    />
                </template>
            </div>
            <template v-if="message">
                <small>{{ message }}</small>
            </template>
        </div>

        <template v-if="visible">
            <component
                :is="editableMapComponent"
                :position="currentPosition"
                :center="currentPosition || center"
                :bounds="currentBounds || bounds"
                :zoom="zoom"
                @map-click="handleMapClicked"
            />
        </template>
    </SharpModal>
</template>

<script>
    import { SharpLoading } from "../../../ui";
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
        },
        props: {
            visible: Boolean,
            position: Object,
            center: Object,
            bounds: Object,
            zoom: Number,
            geocoding: Boolean,
            provider: {
                type: String,
                default: 'gmaps',
            },
        },
        data() {
            return {
                loading: false,
                search: '',
                status: null,

                currentPosition: this.position,
                currentBounds: this.bounds,
            }
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
            },
            editableMapComponent() {
                return getEditableMapByProvider(this.provider);
            },
        },
        methods: {
            handleVisibilityChanged(visible) {
                this.$emit('update:visible', visible);
            },
            handleOkButtonClicked() {
                this.$emit('submit', this.currentPosition);
            },
            handleSearchInput(search) {
                this.search = search;
            },
            handleMapClicked(position) {
                this.currentPosition = position;
            },
            handleSearchSubmitted(e) {
                this.loading = true;
                geocode(this.provider, e.target.value)
                    .then(result => {
                        this.currentPosition = result[0].location;
                        this.currentBounds = result[0].bounds;
                    })
                    .catch(e => {
                        console.log(e);
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
        },
    }

</script>
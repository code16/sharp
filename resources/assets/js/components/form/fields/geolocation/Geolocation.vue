<template>
    <div class="SharpGeolocation">
        <template v-if="!loaded">
            {{ l('form.geolocation.loading') }}
        </template>
        <template v-else-if="!value">
            <SharpButton outline class="w-100" v-b-modal="modalId">
                {{ l('form.geolocation.browse_button') }}
            </SharpButton>
        </template>
        <template v-else>
            <SharpCard light class="SharpModule--closeable"
                :has-close="!readOnly"
                @close-click="handleRemoveButtonClicked"
            >
                <div class="row">
                    <div class="col-7">
                        <slot />
                    </div>
                    <div class="col-5 pl-0">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <div><small>Latitude : {{ latLngString.lat }}</small></div>
                                <div><small>Longitude : {{ latLngString.lng }}</small></div>
                            </div>
                            <div>
                                <SharpButton small outline type="danger" class="remove-button" :disabled="readOnly" @click="handleRemoveButtonClicked">
                                    {{ l('form.geolocation.remove_button') }}
                                </SharpButton>
                                <SharpButton small outline :disabled="readOnly" v-b-modal="modalId">
                                    {{ l('form.geolocation.edit_button') }}
                                </SharpButton>
                            </div>
                        </div>
                    </div>
                </div>
            </SharpCard>
        </template>
    </div>
</template>

<script>
    import { dd2dms } from "./util";
    import SharpMap from './Map.vue';
    import { SharpCard, SharpButton } from "../../../ui";

    export default {
        components: {
            SharpMap,
            SharpCard,
            SharpButton
        },
        props: {
            loaded: false,
            value: Object,
            displayUnit: {
                type: String,
                default: 'DD',
                validator: unit => unit==='DMS' || unit==='DD'
            }
        },
        computed: {
            latLngString() {
                if(this.displayUnit === 'DMS') {
                    return {
                        lat: dd2dms(this.value.lat),
                        lng: dd2dms(this.value.lng, true)
                    }
                }
                else if(this.displayUnit === 'DD') {
                    return this.value;
                }
            }
        },
        methods: {
            handleInput(value) {
                this.$emit('input', value);
            },
            handleRemoveButtonClicked() {
                this.$emit('input', null);
            },
        }
    }
</script>

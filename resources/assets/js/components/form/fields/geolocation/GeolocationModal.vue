<template>
    <SharpModal
        :id="modalId"
        :title="lSub(geocoding ? 'title' : 'title-no-geocoding')"
        no-close-on-backdrop
        @show="handleModalOpened"
        @hidden="handleModalClosed"
        @ok="handleModalOk"
    >
        <div v-if="geocoding" class="mb-2">
            <div class="position-relative">
                <SharpText v-model="search" :placeholder="lSub('geocode_input.placeholder')" @keyup.native.enter="handleGeocodeChanged"/>
                <SharpLoading
                    v-show="loading" small inline visible
                    class="position-absolute m-auto"
                    style="top:0;right:0;bottom:0">
                </SharpLoading>
            </div>
            <div v-if="message"><small>{{ message }}</small></div>
        </div>

        <GmapMap
            class="mw-100"
            style="padding-bottom: 80%;"
            :center="center"
            :zoom="zoom"
            :options="defaultMapOptions"
            @click="handleMapClicked"
            ref="map"
        >
            <GmapMarker
                v-if="position"
                :position="position"
                draggable
            >
            </GmapMarker>
        </GmapMap>
    </SharpModal>
</template>

<script>
    import { SharpLoading } from "../../../ui/index";
    import { LocalizationBase } from '../../../../mixins/index';



</script>
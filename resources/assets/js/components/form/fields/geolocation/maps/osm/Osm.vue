<template>
    <LMap
        style="padding-bottom: 80%"
        :zoom="10"
        :center="[0,0]"
    >
        <LTileLayer url="http://{s}.tile.osm.org/{z}/{x}/{y}.png" />
    </LMap>
</template>

<script>
    import { LMap, LTileLayer, LMarker } from 'vue2-leaflet';

    export default {
        name: 'SharpOsm',
        components: {
            LMap,
            LMarker,
            LTileLayer,
        },
    }
</script>
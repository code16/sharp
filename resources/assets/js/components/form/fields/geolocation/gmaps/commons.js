
export default {
    computed: {
        defaultMapOptions:() => ({
            mapTypeControl:false,
            streetViewControl:false
        }),
    },
    methods: {
        isLatLngInstance(latLng) {
            return latLng instanceof google.maps.LatLng;
        },
        refreshMap() {
            if(this.$refs.map) {
                google.maps.event.trigger(this.$refs.map.$mapObject, 'resize');
            }
            else console.log('Geolocation : no $refs map');
        }
    }
}
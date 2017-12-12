function dd2dms(D, lng){
    let dir = D<0?lng?'W':'S':lng?'E':'N',
        deg = 0|(D<0?D=-D:D),
        min = 0|D%1*60,
        sec = (0|D*60%1*6000)/100;

    return `${deg}°${min}"${sec}' ${dir}`;
}

export default {
    computed: {
        defaultMapOptions:() => ({
            mapTypeControl:false,
            streetViewControl:false
        }),
    },
    methods: {
        latLng2DMS(latLng) {
            let pos = this.isLatLngInstance(latLng) ? latLng.toJSON() : latLng;
            return {
                lat: dd2dms(pos.lat),
                lng: dd2dms(pos.lng, true)
            }
        },
        latLng2DD(latLng) {
            return this.isLatLngInstance(latLng) ? latLng.toJSON() : latLng;
        },
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
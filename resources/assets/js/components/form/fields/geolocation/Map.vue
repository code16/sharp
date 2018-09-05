<script>
    import SharpGmapsGeolocation from './gmaps/GmapsGeolocation.vue';
    import SharpOsmGeolocation from './osm/OsmGeolocation.vue';

    const componentsMap = {
        'gmaps': SharpGmapsGeolocation,
        'osm': SharpOsmGeolocation
    };

    function component(provider) {
        return provider ? componentsMap[provider] : null;
    }

    export default {
        functional: true,
        props: {
            provider: {
                type: String,
                default: 'osm'
            }
        },
        render(h, { props, data }) {
            return h(component(props.provider), data);
        }
    }
</script>
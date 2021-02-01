export default {
    methods : {
        getMergedIdentifier(prop, curId) {
            let ascendant = this.$parent;
            while(ascendant && ascendant[prop] == null) {
                ascendant = ascendant.$parent;
            }
            let ascendantIdentifier = '';
            if(ascendant) {
                ascendantIdentifier = ascendant[prop]+'.';
            }

            return `${ascendantIdentifier}${curId}`;
        }
    }
}
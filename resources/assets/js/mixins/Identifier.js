export default {
    methods : {
        getMergedIdentifier(prop, curId) {
            let ascendant = this.$parent;
            while(ascendant && typeof ascendant[prop] === 'undefined') {
                ascendant = ascendant.$parent;
            }
            let ascendantIdentifier = '';
            if(ascendant?.[prop]) {
                ascendantIdentifier = ascendant[prop]+'.';
            }

            return `${ascendantIdentifier}${curId}`;
        }
    }
}

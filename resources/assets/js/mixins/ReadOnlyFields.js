export default fieldsProp => ({
    computed: {
        readOnlyFields() {
            let res = JSON.parse(JSON.stringify(this[fieldsProp]));
            for(let fieldKey of Object.keys(res)) {
                res[fieldKey].readOnly = true;
            }
            return res;
        }
    }
});
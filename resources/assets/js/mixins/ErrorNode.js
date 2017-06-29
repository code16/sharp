export default {
    props:{
        errorIdentifier:{
            type:[String, Number],
            required:true
        }
    },
    computed: {
        mergedErrorIdentifier() {
            let errorComp = this.$parent;
            while(errorComp && errorComp.mergedErrorIdentifier == null) {
                errorComp = errorComp.$parent;
            }
            let ascendantIdentifier = '';
            if(errorComp) {
                ascendantIdentifier = errorComp.mergedErrorIdentifier+'.';
            }

            return `${ascendantIdentifier}${this.errorIdentifier}`;
        }
    }
}
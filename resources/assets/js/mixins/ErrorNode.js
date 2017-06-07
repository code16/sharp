export default {
    props:{
        errorIdentifier:{
            type:[String, Number],
            required:true
        },
        isErrorRoot: Boolean
    },
    computed: {
        mergedErrorIdentifier() {
            if(this.isErrorRoot)
                return this.errorIdentifier;

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
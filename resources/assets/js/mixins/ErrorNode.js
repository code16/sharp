import Identifier from './utils/Identifier';

export default {
    mixins: [Identifier],
    props:{
        errorIdentifier:{
            type:[String, Number],
            required:true
        }
    },
    computed: {
        mergedErrorIdentifier() {
            return this.getMergedIdentifier('mergedErrorIdentifier', this.errorIdentifier);
        }
    }
}
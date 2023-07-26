import Identifier from './Identifier';

export default {
    mixins: [Identifier],
    props: {
        configIdentifier:{
            type:String,
            required:true
        }
    },
    computed: {
        mergedConfigIdentifier() {
            return this.getMergedIdentifier('mergedConfigIdentifier', this.configIdentifier);
        }
    }
}

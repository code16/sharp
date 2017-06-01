<script>
    import bTab from './vendor/bootstrap-vue/components/tab';
    export default {
        name:'SharpBTab',
        extends:bTab,
        provide() {
            return  {
                $tab:this
            }
        },
        data() {
            return  {
                errors: {}
            }
        },
        computed: {
            hasError() {
                return Object.keys(this.errors).length > 0;
            }
        },
        methods: {
            setError(fieldKey) {
                this.$set(this.errors,fieldKey,true);
            },
            clearError(fieldKey) {
                this.$delete(this.errors,fieldKey);
            }
        }
    }
</script>
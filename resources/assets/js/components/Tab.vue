<script>
    import bTab from './form/vendor/bootstrap-vue/components/tab';
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
        methods: {
            setError(fieldKey) {
                this.errors[fieldKey] = true;
            },
            clearError(fieldKey) {
                this.$delete(this.errors,fieldKey);
            }
        }
    }
</script>
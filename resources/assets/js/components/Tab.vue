<script>
    import Tab from 'bootstrap-vue/es/components/tabs/tab';

    export default {
        name:'SharpBTab',
        extends: Tab,
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
        watch: {
            localActive: {
                immediate: true,
                async handler(val) {
                    if(val) {
                        await this.$nextTick();
                        this.$emit('active');
                    }
                }
            }
        },
        methods: {
            setError(fieldKey) {
                this.$set(this.errors,fieldKey,true);
            },
            clearError(fieldKey) {
                this.$delete(this.errors,fieldKey);
            }
        },
        created() {
            this.$on('error', key=>this.setError(key));
            this.$on('clear', key=>this.clearError(key));
        },
    }
</script>
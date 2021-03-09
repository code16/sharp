<template>
    <b-tab :title-link-class="classes">
        <template v-slot:title>
            {{ title }}
        </template>

        <slot />
    </b-tab>
</template>

<script>
    import { BTab } from 'bootstrap-vue';

    export default {
        name: 'SharpTab',
        components: {
            BTab
        },
        provide() {
            return {
                $tab: this
            }
        },
        props: {
            title: String,
        },
        data() {
            return  {
                errors: {}
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
        computed: {
            hasError() {
                return Object.keys(this.errors).length > 0;
            },
            classes() {
                return {
                    'is-invalid': this.hasError,
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

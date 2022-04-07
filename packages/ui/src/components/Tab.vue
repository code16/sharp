<template>
    <b-tab
        :title-link-class="classes"
        :active="active"
        @update:active="handleActiveChanged"
    >
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
            active: Boolean,
        },
        data() {
            return  {
                errors: {},
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
            },
            async handleActiveChanged(active) {
                if(active) {
                    await this.$nextTick();
                    this.$emit('active');
                }
            },
        },
        created() {
            this.$on('error', key=>this.setError(key));
            this.$on('clear', key=>this.clearError(key));
        },
    }
</script>

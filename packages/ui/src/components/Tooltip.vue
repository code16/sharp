<template>
    <b-tooltip
        :target="target"
        :triggers="triggers"
        :placement="placement"
        :disabled="!isEnabled"
    >
        <slot />
    </b-tooltip>
</template>

<script>
    import { BTooltip } from 'bootstrap-vue';

    export default {
        components: {
            BTooltip,
        },
        props: {
            target: Function,
            triggers: String,
            placement: String,
            overflowOnly: Boolean,
            disabled: Boolean,
        },
        data() {
            return {
                isOverflowing: false,
            }
        },
        computed: {
            isEnabled() {
                if(this.disabled) {
                    return false;
                }
                if(this.overflowOnly) {
                    return this.isOverflowing;
                }
                return true;
            },
        },
        methods: {
            layout(target) {
                this.isOverflowing = target.scrollWidth > target.offsetWidth;
            },
            getTarget() {
                return this.target();
            },
        },
        async mounted() {
            await this.$nextTick();
            const target = this.getTarget();
            this.layout(target);
            if('ResizeObserver' in window) {
                this.observer = new ResizeObserver(() => this.layout(target));
                this.observer.observe(target);
            }
        },
        beforeDestroy() {
            this.observer?.disconnect();
        }
    }
</script>

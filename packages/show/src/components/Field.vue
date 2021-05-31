<template>
    <div class="show-field" :class="classes" v-show="isVisible">
        <component
            :is="component"
            :field-key="options.key"
            :field-config-identifier="mergedConfigIdentifier"
            :value="value"
            :layout="layout"
            :collapsable="collapsable"
            :root="root"
            v-bind="props"
            @visible-change="handleVisiblityChanged"
        />
    </div>
</template>

<script>
    import { getFieldByType } from "./fields";
    import { ConfigNode } from "sharp/mixins";

    export default {
        mixins: [ConfigNode],
        props: {
            value: {},
            options: Object,
            layout: Object,
            collapsable: Boolean,
            root: {
                type: Boolean,
                default: true,
            },
        },
        data() {
            return {
                visible: true,
            }
        },
        computed: {
            component() {
                return this.options
                    ? getFieldByType(this.options.type)
                    : null;
            },
            props() {
                return {
                    ...this.options,
                }
            },
            classes() {
                return [
                    `show-field--${this.options.type}`,
                ];
            },
            isVisible() {
                return !!this.component && this.visible;
            }
        },
        methods: {
            handleVisiblityChanged(visible) {
                this.visible = visible;
                this.$emit('visible-change', visible);
            },
        },
    }
</script>

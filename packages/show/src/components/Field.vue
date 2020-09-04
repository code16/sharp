<template>
    <div class="show-field" v-show="isVisible">
        <component
            :is="component"
            :field-key="options.key"
            :field-config-identifier="mergedConfigIdentifier"
            :value="value"
            :layout="layout"
            v-bind="props"
            @visible-change="handleVisiblityChanged"
        />
    </div>
</template>

<script>
    import { getFieldByType } from "./fields";
    import { ConfigNode } from "sharp/mixins";
    import {syncVisibility} from "../util/fields/visiblity";

    export default {
        mixins: [ConfigNode],
        props: {
            value: {},
            options: Object,
            layout: Object,
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
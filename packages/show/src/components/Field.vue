<template>
    <div class="show-field">
        <template v-if="isVisible">
            <component
                :is="component"
                :field-key="options.key"
                :field-config-identifier="mergedConfigIdentifier"
                :value="value"
                :layout="layout"
                v-bind="props"
            />
        </template>
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
                if(!this.component) {
                    return false;
                }
                if(this.options.type === 'picture') {
                    return !!this.value;
                }
                return true;
            }
        }
    }
</script>
<template>
    <div class="show-field">
        <template v-if="isVisible">
            <component
                :is="component"
                :value="value"
                v-bind="props"
            />
        </template>

    </div>
</template>

<script>
    import { getFieldByType } from "./fields";

    export default {
        props: {
            options: Object,
            value: null,
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
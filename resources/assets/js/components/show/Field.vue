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
                return getFieldByType(this.options.type);
            },
            props() {
                return {
                    ...this.options,
                }
            },
            isVisible() {
                if(!this.value) {
                    if(this.options.type === 'picture') {
                        return false;
                    }
                }
                return true;
            }
        }
    }
</script>
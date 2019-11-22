<template>
    <div class="show-field">
        <component
            :is="component"
            :value="value"
            v-bind="props"
        />
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
            }
        }
    }
</script>
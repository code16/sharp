<template>
    <component v-if="curTemplate.exists"
               :is="curTemplate.compName">
    </component>
</template>

<script>
    import { Template } from '../mixins';

    export default {
        name: 'SharpTemplate',
        mixins: [ Template ],
        props: {
            fieldKey: String,
            name: String
        },
        computed: {
            curTemplate() {
                return this.template();
            }
        }
    }
</script>
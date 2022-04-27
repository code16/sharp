<template>
    <component class="embed" :is="embedOptions.tag">
        <EmbedRenderer
            :data="embedData"
            :options="embedOptions"
        />
    </component>
</template>

<script>
    import { ignoreVueElement } from "sharp";
    import { EmbedRenderer } from 'sharp-embeds';

    export default {
        components: {
            EmbedRenderer,
        },
        props: {
            embedData: Object,
            embedOptions: Object,
        },
        created() {
            ignoreVueElement(this.embedOptions.tag);
        },
    }
</script>

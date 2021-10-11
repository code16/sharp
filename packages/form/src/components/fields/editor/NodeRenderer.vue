<template>
    <component
        :is="tag"
        :class="decorationClasses.value"
        style="white-space: normal"
        data-node-view-wrapper
        v-bind="attributes"
        @dragstart="onDragStart"
    >
        <slot />
    </component>
</template>

<script>
    import { NodeViewWrapper } from '@tiptap/vue-2';

    export default {
        extends: NodeViewWrapper,
        props: {
            node: Object,
        },
        computed: {
            rendered() {
                return this.node.type.spec.toDOM(this.node);
            },
            tag() {
                return this.rendered[0];
            },
            attributes() {
                return this.rendered[1];
            },
        },
        created() {
            if(this.tag) {

            }
        },
    }
</script>

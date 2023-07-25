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
    import { ignoreVueElement } from "sharp";
    import { NodeViewWrapper } from '@tiptap/vue-3';

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
                if(this.rendered instanceof HTMLElement) {
                    return this.rendered.tagName;
                }
                return this.rendered[0];
            },
            attributes() {
                if(this.rendered instanceof HTMLElement) {
                    return [...this.rendered.attributes].reduce((res, attr) => ({
                        ...res,
                        [attr.name]: attr.value,
                    }), {});
                }
                return this.rendered[1];
            },
        },
        created() {
            ignoreVueElement(this.tag);
        },
    }
</script>

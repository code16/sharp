<template>
    <NodeViewWrapper>
        <div class="card editor__node" :class="{ 'shadow border-primary': selected }">
            <div class="card-body">
                <pre class="mb-0">{{ node.attrs.content }}</pre>
            </div>
        </div>
    </NodeViewWrapper>
</template>

<script>
    import { NodeViewWrapper } from '@tiptap/vue-2';

    export default {
        components: {
            NodeViewWrapper,
        },
        props: {
            node: Object,
            editor: Object,
            updateAttributes: Function,
            selected: Boolean,
        },
        methods: {
            handleInput(e) {
                this.updateAttributes({
                    content: e.target.value,
                });
                console.log(JSON.stringify(this.editor.getMarkdown()));
            },
        },
    }
</script>

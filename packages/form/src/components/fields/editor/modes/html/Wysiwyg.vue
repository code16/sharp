<template>
    <div>
        <LocalizedEditors
            :editor="editor"
            :name="uniqueIdentifier"
            :value="value"
            :locale="locale"
            :locales="locales"
            :create-editor="createEditor"
            v-slot="{ editor, locale }"
        >
            <SharpEditor
                :editor="editor"
                v-bind="$props"
                @update="handleUpdate({ editor, locale, ...$event })"
            />
        </LocalizedEditors>
    </div>
</template>

<script>
    import { Editor } from '@tiptap/vue-3';
    import SharpEditor from '../../Editor.vue';
    import { defaultEditorOptions, editorProps } from "../..";
    import { LocalizedEditor } from "../../../../../mixins/localize/editor";
    import { normalizeHTML, trimHTML } from "./util";
    import LocalizedEditors from "../../LocalizedEditors.vue";
    import { normalizeText } from "../../../../../util/text";

    export default {
        mixins: [
            LocalizedEditor
        ],
        components: {
            SharpEditor,
            LocalizedEditors,
        },
        props: {
            ...editorProps,
            extensions: Array,

            readOnly: Boolean,
            uniqueIdentifier: String,
        },
        data() {
            return {
                editor: null,
            }
        },
        methods: {
            handleUpdate({ editor, locale, error }) {
                const content = normalizeText(trimHTML(editor.getHTML(), { inline: this.inline }));
                this.$emit('input', this.localizedValue(content, locale), { error });
            },

            createEditor({ content }) {
                return new Editor({
                    ...defaultEditorOptions,
                    extensions: this.extensions,
                    content: normalizeHTML(content),
                    editable: !this.readOnly,
                });
            },
        },
        created() {
            if(!this.isLocalized) {
                this.editor = this.createEditor({
                    content: this.localizedText,
                });
            }
        },
        beforeDestroy() {
            this.editor?.destroy();
        },
    }
</script>

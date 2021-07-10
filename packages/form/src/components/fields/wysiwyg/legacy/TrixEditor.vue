<template>
    <div class="SharpTrix editor" :class="{ 'SharpTrix--read-only': readOnly }">
        <div class="card">
            <trix-toolbar v-if="toolbar" class="card-header editor__toolbar" :id="toolbarId">
                <trix-custom-toolbar :toolbar="toolbar" />
            </trix-toolbar>
            <trix-editor class="card-body form-control"
                :input="inputId"
                :toolbar="toolbarId"
                :placeholder="placeholder"
                :style="{ height: `${height}px`, maxHeight:`${height}px` }"
                @trix-change="handleChanged"
                @trix-before-paste="handleBeforePaste"
                ref="trix"
            ></trix-editor>
        </div>
        <input :id="inputId" :value="text" type="hidden">
    </div>
</template>

<script>
    import TrixCustomToolbar from './TrixCustomToolbar.vue';

    import localize from '../../../mixins/localize/editor';
    import { onLabelClicked } from "../../../util/accessibility";
    import { normalizeText } from "../../../util/text";

    export default {
        name:'SharpTrix',

        mixins: [ localize({ textProp:'text' }) ],

        components: {
            TrixCustomToolbar
        },
        props: {
            id: String,
            value: Object,
            toolbar: Array,
            height: {
                type: Number,
                default: 250
            },
            placeholder: String,
            readOnly: Boolean,
            uniqueIdentifier: String,

        },
        watch: {
            locale() {
                this.localized && this.$refs.trix.editor.loadHTML(this.text);
            }
        },
        computed: {
            inputId() {
                return `trix-input-${this.uniqueIdentifier}`;
            },
            toolbarId() {
                return `trix-toolbar-${this.uniqueIdentifier}`;
            },
            text() {
                return this.localized ? this.localizedText : this.value.text;
            }
        },
        methods: {
            handleChanged(event) {
                this.$emit('input', this.localizedValue(event.target.value));
            },
            handleBeforePaste(e) {
                if(e.paste.string) {
                    e.paste.string = normalizeText(e.paste.string);
                }
                if(e.paste.html) {
                    e.paste.html = normalizeText(e.paste.html);
                }
            },
        },
        created() {
            window.Trix.config.toolbar.getDefaultHTML = () => '';
        },
        mounted() {
            onLabelClicked(this, this.id, () => {
                this.$refs.trix.focus();
            });
        }
    }
</script>

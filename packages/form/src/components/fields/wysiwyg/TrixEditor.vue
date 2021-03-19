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
                ref="trix"
            ></trix-editor>
        </div>
        <input :id="inputId" :value="text" type="hidden">
    </div>
</template>

<script>
    import TrixCustomToolbar from './TrixCustomToolbar.vue';

    import localize from '../../../mixins/localize/editor';

    export default {
        name:'SharpTrix',

        mixins: [ localize({ textProp:'text' }) ],

        components: {
            TrixCustomToolbar
        },
        props: {
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
            }
        },
        created() {
            window.Trix.config.toolbar.getDefaultHTML = () => '';
        }
    }
</script>

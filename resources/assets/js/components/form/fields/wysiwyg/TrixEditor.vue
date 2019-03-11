<template>
    <div class="SharpTrix" :class="{ 'SharpTrix--read-only': readOnly }">
        <div class="SharpModule__inner">
            <input :id="inputId" :value="text" type="hidden">
            <trix-toolbar v-if="toolbar" class="SharpModule__header" :id="toolbarId">
                <trix-custom-toolbar :toolbar="toolbar" />
            </trix-toolbar>
            <trix-editor class="SharpModule__content"
                :input="inputId"
                :toolbar="toolbarId"
                :placeholder="placeholder"
                :style="{ height: `${height}px`, maxHeight:`${height}px` }"
                @trix-change="handleChanged"
                ref="trix"
            ></trix-editor>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import Trix from 'trix';

    import TrixCustomToolbar from './TrixCustomToolbar.vue';

    import localize from '../../../../mixins/localize/editor';

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
            Trix.config.toolbar.getDefaultHTML = () => '';
        }
    }
</script>
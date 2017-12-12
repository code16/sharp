<template>
    <div class="SharpTrix" :class="{ 'SharpTrix--read-only': readOnly }">
        <div class="SharpModule__inner">
            <input :id="inputId" :value="value.text" type="hidden">
            <trix-toolbar v-if="toolbar" class="SharpModule__header" :id="toolbarId">
                <trix-custom-toolbar :toolbar="toolbar" />
            </trix-toolbar>
            <trix-editor class="SharpModule__content"
                :input="inputId"
                :toolbar="toolbarId"
                :placeholder="placeholder"
                :style="{ height: `${height}px`, maxHeight:`${height}px` }"
                @trix-change="handleChanged"
            ></trix-editor>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import Trix from 'trix';

    import TrixCustomToolbar from './TrixCustomToolbar.vue';

    export default {
        name:'SharpTrix',
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
            uniqueIdentifier: String
        },
        computed: {
            inputId() {
                return `trix-input-${this.uniqueIdentifier}`;
            },
            toolbarId() {
                return `trix-toolbar-${this.uniqueIdentifier}`;
            }
        },
        methods: {
            handleChanged(event) {
                this.$emit('input', { text:event.target.value });
            }
        },
        created() {
            Vue.ignoredElements = Vue.ignoredElements || [];
            if(!Vue.ignoredElements.includes('trix-editor')) {
                Vue.ignoredElements = [
                    ...Vue.ignoredElements, [
                        'trix-editor',
                        'trix-toolbar'
                    ]
                ];
            }
            Trix.config.toolbar.getDefaultHTML = () => '';
        },
        mounted() {
            console.log(Trix);
        }
    }
</script>
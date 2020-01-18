<template>
    <sharp-multiselect
            class="SharpTags"
            :value="tags"
            :options="indexedOptions"
            :placeholder="dynamicPlaceholder"
            :tag-placeholder="createText"
            :max="maxTagCount"
            :taggable="creatable"
            :close-on-select="false"
            :disabled="readOnly"
            track-by="_internalId"
            label="label"
            :custom-label="localizedCustomLabel"
            multiple searchable hide-selected
            :show-labels="false"
            @search-change="handleTextInput"
            @input="handleInput"
            @tag="handleNewTag"
            ref="multiselect">
    </sharp-multiselect>
</template>

<script>
    import SharpMultiselect from '../../Multiselect';
    import localize from '../../../mixins/localize/Tags';

    class LabelledItem {
        constructor(item) {
            this.id = item.id;
            this.label = item.label;
        }

        set internalId(id) {
            this._internalId = id;
        }

        get internalId() {
            return this._internalId;
        }
    }

    class Option extends LabelledItem {
    }
    class Tag extends LabelledItem {
    }

    export default {
        name: 'SharpTags',
        mixins:[localize],
        components: {
            SharpMultiselect
        },
        props: {
            value: Array, // [{id:0, label: 'AAA'}, ...]
            options: Array, // [{id:0, label:'AAA'}, ...]
            placeholder: String,
            maxTagCount: Number,
            createText: String,
            creatable: {
                type: Boolean,
                default: true
            },
            readOnly:Boolean,
        },
        data() {
            return {
                tags: [],
                lastIndex: 0
            }
        },
        computed: {
            indexedOptions() {
                return this.options.map(this.patchOption);
            },
            dynamicPlaceholder() {
                return this.tags.length < (this.maxTagCount || Infinity) ? this.placeholder : "";
            },
            ids() {
                return this.tags.map(t=>t.internalId);
            }
        },
        watch: {
            tags: 'onTagsChanged'
        },
        methods: {
            patchOption(option, index) {
                let patchedOption = new Option(option);
                patchedOption.internalId = index;
                return patchedOption;
            },
            patchTag(tag) {
                let matchedOption = this.indexedOptions.find(o => o.id === tag.id);
                let patchedTag = new Tag(matchedOption);
                patchedTag.internalId = matchedOption.internalId;
                return patchedTag;
            },
            handleNewTag(val) {
                let newTag = new Tag({id: null, label: this.localizedTagLabel(val) });
                newTag.internalId = this.lastIndex++;
                this.tags.push(newTag);
            },
            handleInput(val) {
                this.tags = val;
            },
            handleTextInput(text) {
                if (text.length > 0 && this.$refs.multiselect.filteredOptions.length > 1) {
                    this.$refs.multiselect.pointer = 1;
                }
                else this.$refs.multiselect.pointer = 0
            },
            onTagsChanged() {
                this.$emit('input', this.tags.map(t => new Tag(t)));
            }
        },
        created() {
            this.lastIndex += this.options.length;
            this.tags = (this.value||[]).map(this.patchTag);
        }
    }
</script>
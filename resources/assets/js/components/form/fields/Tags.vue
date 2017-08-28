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
            multiple searchable hide-selected
            selectLabel="" selectedLabel="" deselectLabel=""
            @search-change="handleTextInput"
            @input="handleInput"
            @tag="handleNewTag"
            ref="multiselect">
    </sharp-multiselect>
</template>

<script>
    import Multiselect from '../../Multiselect';

    class LabelledItem {
        constructor(item, keep={}) {
            this.id = item.id;
            if(keep.label) {
                this.label = item.label
            }
            if(keep.internalId) {
                this.internalId = item.internalId;
            }
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

        constructor(item, { toExport } = {}) {
            super(item, { label: !toExport || !item.id, internalId: !toExport });
        }
    }

    export default {
        name: 'SharpTags',
        components: {
            [Multiselect.name]: Multiselect
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
            croppedTags() {
                return this.tags.maps(({ id, label }) => {
                    let cropped = {};
                    cropped.id = id;
                    if(!id) cropped.label = label;
                    return cropped;
                });
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
                return new Tag(matchedOption);
            },
            handleNewTag(val) {
                let newTag = new Tag({id: null, label: val});
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
                this.$emit('input', this.tags.map(t => new Tag(t, { toExport: true })));
            }
        },
        created() {
            this.lastIndex += this.options.length;
            this.tags = (this.value||[]).map(this.patchTag);
        }
    }
</script>
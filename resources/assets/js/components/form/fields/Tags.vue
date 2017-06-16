<template>
    <sharp-multiselect class="SharpTags"
                 :value="tags"
                 :options="indexedOptions"
                 :placeholder="placeholder"
                 :tag-placeholder="createText"
                 :max="maxTagCount"
                 :taggable="creatable"
                 :close-on-select="false"
                 track-by="_internalId"
                 label="label"
                 multiple searchable hide-selected
                 selectLabel="" selectedLabel="" deselectLabel=""
                 @search-change="handleTextInput"
                 @input="handleInput"
                 @tag="handleNewTag"
                 ref="multiselect">
        <template slot="maxElements">{{maxText}}</template>
    </sharp-multiselect>
</template>

<script>
    import Multiselect from '../../Multiselect';

    class LabelledItem {
        constructor(item) {
            this.id = item.id;
            this.label = item.label;
        }
        set internalId(id) { this._internalId = id; }
        get internalId() { return this._internalId; }
    }

    class Option extends LabelledItem { }
    class Tag extends LabelledItem { }

    export default {
        name:'SharpTags',
        components: {
            [Multiselect.name]:Multiselect
        },
        props : {
            value:Array,
            options:Array,
            placeholder:String,
            maxTagCount:Number,
            maxText:String,
            createText:String,
            creatable:true,
        },
        data() {
            return {
                tags:[],
                lastIndex:0
            }
        },
        computed: {
            indexedOptions() {
                return this.options.map(this.patchOption);
            },
        },
        watch: {
            tags:'onTagsChanged'
        },
        methods: {
            patchOption(option, index) {
                let patchedOption = new Option(option);
                patchedOption.internalId = index;
                return patchedOption;
            },
            patchTag(tag) {
                let matchedOption = this.indexedOptions.find(o=>o.id===tag.id);
                let patchedTag = new Tag(tag);
                patchedTag.internalId = matchedOption ? matchedOption.internalId : this.lastIndex++;
                return patchedTag;
            },
            handleNewTag(val) {
                let newTag = new Tag({id:null, label:val});
                newTag.internalId = this.lastIndex++;
                this.tags.push(newTag);
            },
            handleInput(val) {
                this.tags = val;
            },
            handleTextInput(text) {
                if(text.length > 0 && this.$refs.multiselect.filteredOptions.length > 1) {
                    this.$refs.multiselect.pointer=1;
                }
                else this.$refs.multiselect.pointer=0
            },
            onTagsChanged() {
                this.$emit('input',this.tags.map(t => new Tag(t)));
            }
        },
        created() {
            this.lastIndex += this.options.length;
            this.tags = this.value.map(this.patchTag);
        }
    }
</script>
<template>
    <multiselect class="SharpTags"
                 :value="tags"
                 :options="indexedOptions"
                 :placeholder="placeholder"
                 :tag-placeholder="createText"
                 :max="maxTagCount"
                 :taggable="creatable"
                 :close-on-select="false"
                 track-by="internalId"
                 label="label"
                 multiple searchable hide-selected
                 selectLabel="" selectedLabel="" deselectLabel=""
                 @input="handleInput"
                 @tag="handleNewTag">
        <template slot="maxElements">
            Maximum de {{maxTagCount}} éléments atteint
        </template>
    </multiselect>
</template>

<script>
    import Multiselect from 'vue-multiselect';

    export default {
        name:'SharpTags',
        components: {
            Multiselect
        },
        props : {
            value:Array,
            options:Array,
            placeholder:String,
            maxTagCount:Number,
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
                return this.patch(this.options);
            },
        },
        watch: {
            tags(tags) {
                if(this.lastIndex) {
                    this.$emit('input',tags.map(t =>({id:t.id, label:t.label})));
                }
            }
        },
        methods: {
            patch(array) {
                return array.map((o,i) => {
                    o.internalId = i;
                    return o;
                });
            },
            handleNewTag(val) {
                this.tags.push({id:null,label:val,internalId:this.lastIndex++});
            },
            handleInput(val) {
                this.tags = val;
            }
        },
        mounted() {
            this.tags = this.patch(this.value);
            this.lastIndex = this.options.length + this.tags.length;
        }
    }
</script>
<template>
    <multiselect class="SharpTags"
                 :value="value"
                 :options="options"
                 :placeholder="placeholder"
                 :tag-placeholder="createText"
                 :max="maxTagCount"
                 :taggable="creatable"
                 :close-on-select="false"

                 label="label"
                 multiple searchable hide-selected
                 selectLabel="" selectedLabel="" deselectLabel=""
                 @input="v=>$emit('input',v)"
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
        methods: {
            handleNewTag(val) {
                this.value.push({id:null,label:val});
            }
        }
    }
</script>
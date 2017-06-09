<template>
    <multiselect class="SharpTags"
                 :value="value"
                 :options="options"
                 :placeholder="placeholder"
                 :tag-placeholder="createText"
                 :max="maxTagCount"
                 :taggable="creatable"
                 searchable hide-selected
                 @input="v=>$emit('input',v)">
    </multiselect>
</template>

<script>
    import Multiselect from 'vue-multiselect';

    export default {
        name:'SharpTags',
        components: {
            Multipleselect
        },
        props : {
            options:Array,
            placeholder:String,
            tagMaxCount:Number,
            createText:String,
            creatable:true,
        }
    }
</script>
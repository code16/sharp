<template>
    <div>
        <sharp-template :field-key="fieldKey" name="result-item"/>
        <el-autocomplete v-model="value" trigger-on-focus
                         :fetch-suggestions="collectSuggestions"
                         :placeholder="placeholder"
                         :custom-item="listItemTemplate.compNameOrDefault"
                         :disabled="disabled">
        </el-autocomplete>
    </div>
</template>

<script>
    import SharpTemplate from '../Template.vue';
    import { Autocomplete } from 'element-ui';

    import { Template } from '../../mixins';

    export default {
        name:'SharpAutocomplete',

        mixins: [Template],
        components: {
            [Autocomplete.name]:Autocomplete,
            [SharpTemplate.name]:SharpTemplate
        },

        props: {
            fieldKey: String,

            mode: String,
            localValues: Array,
            placeholder: String,
            remoteEndPoint: String,
            remoteMethod:String,
            remoteSearchAttribute: {
                type: String,
                default: 'query'
            },
            itemKeyAttribute:String,
            searchMinBar: {
                type: Number,
                default: 1
            },
            disabled:Boolean
        },
        data() {
            return {
                value:'',
                suggestions:this.localValues
            }
        },
        computed: {
            isRemote() {
                return !!this.remoteEndPoint;
            },
            listItemTemplate() {
                return this.template(this.fieldKey, 'list-item');
            }
        },
        methods: {
            collectSuggestions(querystring, cb) {
                cb([
                    {name:'Antoine', surname:'Guingand'}
                ]);
                if (!this.isRemote)
                    return;

                axios[this.remoteMethod.toLowerCase()](this.remoteEndPoint, {
                    searchAttribute: this.searchAttribute
                })
                    .then(response => {
                        this.options = response.data;
                    })
                    .catch(
                        // some error callback
                    );
            }
        },
        mounted() {
            //debugger;
        }
    }
</script>
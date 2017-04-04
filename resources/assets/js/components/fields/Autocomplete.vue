<template>
    <div>
        <sharp-template :field-key="fieldKey" name="resultItem"/>
        <el-autocomplete v-model="value" trigger-on-focus
                         :fetch-suggestions="collectSuggestions"
                         :placeholder="placeholder"
                         :custom-item="listItemTemplate.compNameOrDefault"
                         @select="handleSelect"
                         :disabled="disabled">
        </el-autocomplete>
    </div>
</template>

<script>
    import SharpTemplate from '../Template.vue';
    import { Autocomplete } from 'element-ui';

    import Template from '../../app/models/Template';
    import SearchStrategy from '../../app/models/SearchStrategy';


    export default {
        name:'SharpAutocomplete',
        components: {
            [Autocomplete.name]:Autocomplete,
            [SharpTemplate.name]:SharpTemplate
        },

        props: {
            fieldKey: String,

            mode: String,
            localValues: Array,
            placeholder: String,
            remoteEndpoint: String,
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
                value: '',
                localSearchStrategy:null,
            }
        },
        computed: {
            isRemote() {
                return this.mode === 'remote';
            },
            listItemTemplate() {
                return new Template(this.fieldKey, 'listItem');
            },
            filteredSuggestions() {
                return this.localSearchStrategy.search(this.value);
            }
        },
        methods: {
            collectSuggestions(querystring, cb) {
                if (this.mode === 'local') {
                    cb(this.filteredSuggestions);
                }
                else if(this.mode === 'remote') {
                    axios[this.remoteMethod.toLowerCase()](this.remoteEndpoint, {
                        searchAttribute: this.searchAttribute
                    }).then(response => {
                        cb(response.data);
                    }).catch(
                        // some error callback
                    );
                }
            },
            handleSelect(item) {
                console.log(item);
            }
        },
        mounted() {
            if(this.mode === 'local') {
                this.localSearchStrategy = new SearchStrategy({
                    list:this.localValues,
                    minQueryLength:2
                });
            }
        }
    }
</script>
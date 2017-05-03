<template>
    <div class="SharpAutocomplete">
        <multiselect v-model="value" :options="labelledSuggestions" label="__searchLabel" :track-by="itemIdAttribute"
                     :internal-search="localSearch" :placeholder="placeholder" :loading="isLoading"
                     @search-change="updateSuggestions" ref="multiselect">
            <template slot="option" scope="props">
                <sharp-template :field-key="fieldKey" :template="listItemTemplate"
                                :template-data="props.option" name="listItem"></sharp-template>
            </template>
        </multiselect>
        <sharp-template class="SharpAutocomplete__result-item" :field-key="fieldKey" name="resultItem"
                        :template-data="value" ref="resultItem"></sharp-template>
    </div>
</template>

<script>
    import SharpTemplate from '../Template.vue';
    import Multiselect from 'vue-multiselect';

    import Template from '../../app/models/Template';
    import SearchStrategy from '../../app/models/SearchStrategy';


    export default {
        name:'SharpAutocomplete',
        components: {
            Multiselect,
            [SharpTemplate.name]:SharpTemplate
        },

        props: {
            fieldKey: String,

            mode: String,
            localValues: {
                type: Array,
                default:()=>[]
            },
            placeholder: String,
            remoteEndpoint: String,
            remoteMethod:String,
            remoteSearchAttribute: {
                type: String,
                default: 'query'
            },
            itemIdAttribute: {
                type:String,
                default: 'id'
            },
            searchMinChars: {
                type: Number,
                default: 1
            },
            searchKeys: {
                type: Array,
                default:()=>['value']
            },
            disabled:Boolean,
            listItemTemplate: String
        },
        data() {
            return {
                value: null,
                suggestions: this.localValues,
                isLoading: false,
                localSearch: false,
            }
        },
        computed: {
            isRemote() {
                return this.mode === 'remote';
            },
            labelledSuggestions() {
                return this.suggestions.map(s=>{
                    s.__searchLabel = this.searchKeys.reduce((label, key, i) => {
                        if(i)
                            return `${label} ${s[key]}`;
                        return s[key];
                    },'');
                    return s;
                });
            }
        },
        methods: {
            updateSuggestions(query) {
                if(this.isRemote) {
                    this.isLoading = true;
                    const call = (method,endpoint,attribute) =>
                        method === 'GET' ?
                            axios.get(endpoint,{
                            params: {
                                [attribute]:query
                            }
                        }): axios.post(endpoint,{
                            [attribute]:query
                        }
                    );
                    call(this.remoteMethod, this.remoteEndpoint, this.remoteSearchAttribute)
                        .then(response => {
                            this.isLoading = false;
                            this.suggestions = response.data;
                        }).catch(
                            // some error callback
                        );
                }
                else {
                    this.localSearch = query.length >= this.searchMinChars;
                }
            },
        },
    }
</script>
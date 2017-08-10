<template>
    <div class="SharpAutocomplete"
         :class="[`SharpAutocomplete--${state}`,
                 {'SharpAutocomplete--remote':isRemote},
                 {'SharpAutocomplete--disabled':readOnly}]">
        <div v-if="state=='valuated'" class="SharpAutocomplete__result-item">
            <sharp-template name="ResultItem" :template="resultItemTemplate" :template-data="valueObject"></sharp-template>
            <button class="SharpAutocomplete__result-item__close-button" type="button" @click="handleResetClick">
                <svg class="SharpAutocomplete__result-item__close-icon"
                     aria-label="close" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                    <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                </svg>
            </button>
        </div>
        <multiselect v-if="state!='valuated'"
                     class="SharpAutocomplete__multiselect"
                     :class="{'SharpAutocomplete__multiselect--hide-dropdown':hideDropdown}"
                     :value="valueObject"
                     :options="dynamicSuggestions"
                     :track-by="itemIdAttribute"
                     :internal-search="false"
                     :placeholder="placeholder"
                     :loading="isLoading"
                     :disabled="readOnly"
                     :max="hideDropdown ? -1 : 1"
                     preserve-search
                     @search-change="updateSuggestions"
                     @select="handleSelect"
                     @close="handleDropdownClose"
                     @search-changed="handleSearchChanged"
                     @open="opened=true"
                     ref="multiselect">
            <template slot="option" scope="props">
                <sharp-template name="ListItem" :template="listItemTemplate" :template-data="props.option"></sharp-template>
            </template>
            <template slot="loading">
                <sharp-loading :visible="isLoading" inline small></sharp-loading>
            </template>
            <template slot="noResult">Aucun résultats</template>
        </multiselect>
    </div>
</template>

<script>
    import Template from '../../Template.vue';
    import Loading from '../../Loading.vue';
    import Multiselect from 'vue-multiselect';

    import SearchStrategy from '../../../app/models/SearchStrategy';

    import axios from 'axios';

    export default {
        name:'SharpAutocomplete',
        components: {
            Multiselect,
            [Template.name]:Template,
            [Loading.name]: Loading
        },

        props: {
            fieldKey: String,

            value: [String, Number],

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
            readOnly: Boolean,
            listItemTemplate: String,
            resultItemTemplate: String
        },
        data() {
            return {
                query: '',
                suggestions: this.localValues,
                searchStrategy: new SearchStrategy({
                    list: this.localValues,
                    minQueryLength: this.searchMinChars,
                    searchKeys: this.searchKeys
                }),
                opened: false,
                state: this.value?'valuated':'initial'
            }
        },
        computed: {
            isRemote() {
                return this.mode === 'remote';
            },
            valueObject() {
                if(!this.value)
                    return null;

                if(this.isRemote)
                    return this.value;

                return this.localValues.find(v=>v[this.itemIdAttribute]===this.value);
            },
            isLoading() {
                return this.state === 'loading' || this.opened && !!this.query.length && this.hideDropdown;
            },
            hideDropdown() {
                return this.isRemote ? this.query.length < this.searchMinChars : false;
            },
            dynamicSuggestions() {
                return this.hideDropdown ? [null] : this.suggestions;
            }
        },
        methods: {
            updateSuggestions(query) {
                this.query = query;
                if(this.hideDropdown)
                    return;

                if(this.isRemote) {
                    this.state = 'loading';
                    const call = (method,endpoint,attribute) =>
                        method === 'GET' ?
                            axios.get(endpoint,{
                            params: {
                                [attribute]:query
                            }
                        }): axios.post(endpoint,{
                            [attribute]:query
                        });
                    call(this.remoteMethod, this.remoteEndpoint, this.remoteSearchAttribute)
                        .then(response => {
                            this.state = 'searching';
                            this.suggestions = response.data;
                        }).catch(
                            // some error callback
                        );
                }
                else {
                    this.suggestions = this.searchStrategy.search(query);
                    this.state = 'searching';
                }
            },

            handleSearchChanged(search) {
                this.query = search;
            },

            handleSelect(value) {
                this.state = 'valuated';
                this.$emit('input', value[this.itemIdAttribute]);
            },
            handleDropdownClose() {
                if(this.state === 'searching')
                    this.state = 'initial';
                this.opened = false;
            },
            handleResetClick() {
                this.state = 'searching';

                this.$emit('input', '');
                this.$nextTick(()=>{
                    this.$refs.multiselect.activate();
                });
            }
        },
    }
</script>
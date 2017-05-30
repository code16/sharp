<template>
    <div class="SharpAutocomplete" :class="`SharpAutocomplete--${state}`">
        <div v-if="state=='valuated'" class="SharpAutocomplete__result-item form-control">
            <sharp-template name="resultItem" :template="resultItemTemplate" :template-data="valueObject"></sharp-template>
            <div class="SharpAutocomplete__close-btn-container" @click="handleResetClick">
                <button type="button" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <multiselect v-if="state!='valuated'"
                     :value="valueObject"
                     :options="dynamicSuggestions"
                     :track-by="itemIdAttribute"
                     :internal-search="false"
                     :placeholder="placeholder"
                     :loading="state=='loading'"
                     :max="hideDropdown ? -1 : 1"
                     selectLabel="" selectedLabel="" deselectLabel=""
                     @search-change="updateSuggestions"
                     @select="handleSelect"
                     @input="handleInput"
                     @close="handleDropdownClose"
                     ref="multiselect">
            <template slot="option" scope="props">
                <sharp-template name="listItem" :template="listItemTemplate" :template-data="props.option"></sharp-template>
            </template>
            <template slot="noResult">Aucun résultats</template>
        </multiselect>
    </div>
</template>

<script>
    import SharpTemplate from '../Template.vue';
    import Multiselect from 'vue-multiselect';

    import SearchStrategy from '../../../app/models/SearchStrategy';


    export default {
        name:'SharpAutocomplete',
        components: {
            Multiselect,
            [SharpTemplate.name]:SharpTemplate
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
            disabled: Boolean,
            listItemTemplate: String,
            resultItemTemplate: String
        },
        data() {
            return {
                query: '',
                suggestions: this.localValues,
                isLoading: false,
                searchStrategy: new SearchStrategy({
                    list: this.localValues,
                    minQueryLength: this.searchMinChars,
                    searchKeys: this.searchKeys
                }),
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
            hideDropdown() {
                return this.query.length < this.searchMinChars;
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
                        }
                    );
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

            handleSelect(value) {
                this.state = 'valuated';
                this.$emit('input', value[this.itemIdAttribute]);
            },
            handleInput(value) {

            },
            handleDropdownClose() {
                if(this.state === 'searching')
                    this.state = 'initial';
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
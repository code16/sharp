<template>
    <div class="SharpAutocomplete">
        <div v-if="state=='valuated'" class="SharpAutocomplete__result-item">
            <sharp-template :field-key="fieldKey"
                            name="resultItem" :template-data="value">
            </sharp-template>
            <div class="SharpAutocomplete__close-btn-container" @click="handleCloseClick">
                <button type="button" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <multiselect v-else
                     v-model="value"
                     :options="suggestions"
                     :track-by="itemIdAttribute"
                     :internal-search="false"
                     :placeholder="placeholder"
                     :loading="state=='loading'"
                     selectLabel="" selectedLabel="" deselectLabel=""
                     @search-change="updateSuggestions"
                     @select="handleSelect"
                     ref="multiselect">
            <template slot="option" scope="props">
                <sharp-template :field-key="fieldKey" :template-data="props.option" name="listItem"></sharp-template>
            </template>
        </multiselect>
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
            inline:Boolean,
            disabled:Boolean,
            listItemTemplate: String
        },
        data() {
            return {
                value: null,
                suggestions: this.localValues,
                isLoading: false,
                searchStrategy: new SearchStrategy({
                    list: this.localValues,
                    minQueryLength: this.searchMinChars,
                    searchKeys: this.searchKeys
                }),
                state:'initial'
            }
        },
        computed: {
            isRemote() {
                return this.mode === 'remote';
            }
        },
        methods: {
            updateSuggestions(query) {
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
            handleSelect() {
                this.state = 'valuated';
            },
            handleCloseClick() {
                console.log('focus');
                this.state = this.inline ? 'searching' : 'initial';

                this.value=null;
                this.$nextTick(()=>{
                    this.$refs.multiselect.activate();
                });
            }
        },
    }
</script>
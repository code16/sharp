<template>
    <div class="SharpAutocomplete"
         :class="[`SharpAutocomplete--${state}`,
                 {'SharpAutocomplete--remote':isRemote},
                 {'SharpAutocomplete--disabled':readOnly}]">
        <div v-if="state==='valuated' && value" class="SharpAutocomplete__result-item">
            <sharp-template name="ResultItem" :template="resultItemTemplate" :template-data="localizedTemplateData(value)"></sharp-template>
            <button class="SharpAutocomplete__result-item__close-button" type="button" @click="handleResetClick">
                <svg class="SharpAutocomplete__result-item__close-icon"
                     aria-label="close" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                    <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                </svg>
            </button>
        </div>
        <multiselect
            v-if="state!=='valuated'"
            class="SharpAutocomplete__multiselect"
            :class="{'SharpAutocomplete__multiselect--hide-dropdown':hideDropdown}"
            :value="value"
            :options="suggestions"
            :track-by="itemIdAttribute"
            :internal-search="false"
            :placeholder="placeholder"
            :loading="isLoading"
            :multiple="multiple"
            :disabled="readOnly"
            :hide-selected="hideSelected"
            :allow-empty="allowEmpty"
            :preserve-search="preserveSearch"
            :show-pointer="showPointer"
            @search-change="updateSuggestions($event)"
            @select="handleSelect"
            @input="$emit('multiselect-input',$event)"
            @close="handleDropdownClose"
            @open="handleDropdownOpen"
            ref="multiselect"
        >
            <template slot="option" slot-scope="props">
                <sharp-template name="ListItem" :template="listItemTemplate" :template-data="localizedTemplateData(props.option)"></sharp-template>
            </template>
            <template slot="loading">
                <sharp-loading :visible="isLoading" inline small></sharp-loading>
            </template>
            <template slot="noResult">{{ l('form.autocomplete.no_results_text') }}</template>
        </multiselect>
    </div>
</template>

<script>
    import SharpTemplate from '../../Template.vue';
    import SharpLoading from '../../ui/Loading.vue';
    import Multiselect from 'vue-multiselect';

    import SearchStrategy from '../../../app/models/SearchStrategy';

    import axios from 'axios';

    import { warn, error } from '../../../util';
    import { Localization, Debounce } from '../../../mixins';
    import { lang } from '../../../mixins/Localization';

    import localize from '../../../mixins/localize/Autocomplete';

    export default {
        name:'SharpAutocomplete',
        components: {
            Multiselect,
            SharpTemplate,
            SharpLoading
        },

        mixins: [Localization, Debounce, localize],

        props: {
            fieldKey: String,

            value: [String, Number, Object, Array],

            mode: String,
            localValues: {
                type: Array,
                default:()=>[]
            },
            placeholder: {
                type: String,
                default: () => lang('form.multiselect.placeholder')
            },
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
            resultItemTemplate: String,
            noResultItem: Boolean,
            multiple: Boolean,
            hideSelected: Boolean,
            allowEmpty: {
                type: Boolean,
                default: true
            },
            clearOnSelect: Boolean,
            preserveSearch: {
                type: Boolean,
                default: true
            },
            showPointer: {
                type:Boolean,
                default:true
            }
        },
        data() {
            return {
                query: '',
                suggestions: this.localValues,
                opened: false,
                state: 'initial'
            }
        },
        watch: {
            localValues() {
                this.updateLocalSuggestions({ keepState:true });
            },
            locale() {
                this.initState();
            },
        },
        computed: {
            isRemote() {
                return this.mode === 'remote';
            },
            isLoading() {
                return this.state === 'loading' || this.opened && !!this.query.length && this.hideDropdown;
            },
            hideDropdown() {
                return this.isRemote ? this.query.length < this.searchMinChars : false;
            },
            searchStrategy() {
                return !this.isRemote ? new SearchStrategy({
                    list: this.localValues,
                    minQueryLength: this.searchMinChars,
                    searchKeys: this.localizedSearchKeys
                }) : null;
            },
        },
        methods: {
            callApi(query) {
                let params = { [this.remoteSearchAttribute]:query };
                if(this.localized)
                    params.locale = this.locale;
                return this.remoteMethod === 'GET'
                    ? axios.get(this.remoteEndpoint, { params })
                    : axios.post(this.remoteEndpoint, params)
            },

            updateSuggestions(query) {
                this.query = query;
                if(this.hideDropdown)
                    return;
                if(this.isRemote) {
                    this.state = 'loading';
                    this.updateRemoteSuggestions();
                }
                else this.updateLocalSuggestions();
            },

            updateLocalSuggestions({ keepState }={}) {
                this.suggestions = this.searchStrategy.search(this.query);
                if(!keepState) {
                    this.state = 'searching';
                }
            },
            initState() {
                this.state = this.value ? 'valuated' : 'initial';
            },
            handleSelect(value) {
                this.state = 'valuated';
                this.$emit('input', value);
            },
            handleDropdownClose() {
                if(this.state === 'searching')
                    this.state = 'initial';
                this.opened = false;
                this.$emit('close');
            },
            handleDropdownOpen() {
                this.opened = true;
                this.$emit('open');
            },
            handleResetClick() {
                this.state = 'initial';

                this.$emit('input', null);
                this.$nextTick(()=>{
                    this.$refs.multiselect.activate();
                });
            },
            itemMatchValue(localValue) {
                // noinspection EqualityComparisonWithCoercionJS
                return localValue[this.itemIdAttribute] == this.value[this.itemIdAttribute];
            },
            findLocalValue() {
                if(!this.value || this.value[this.itemIdAttribute] == null) return null;
                if(!this.localValues.some(this.itemMatchValue)) {
                    error(`Autocomplete (key: ${this.fieldKey}) can't find local value matching : ${JSON.stringify(this.value)}`);
                    return null;
                }
                return this.localValues.find(this.itemMatchValue);
            }
        },
        debounced: {
            wait: 200,
            async updateRemoteSuggestions() {
                //console.log('remote');
                try {
                    let { data } = await this.callApi(this.query);
                    this.state = 'searching';
                    this.suggestions = data;
                }
                catch(e) {
                    console.log('error', e)
                }
            },
        },
        async created() {
            if(this.mode === 'local' && !this.searchKeys) {
                warn(`Autocomplete (key: ${this.fieldKey}) has local mode but no searchKeys, default set to ['value']`);
            }

            if(!this.isRemote) {
                this.$emit('input', this.findLocalValue(), { force: true });
            }
            if(this.noResultItem) {
                Object.defineProperty(this, 'state', { get:()=>'initial' });
            }
            await this.$nextTick();
            this.initState();
        }
    }
</script>
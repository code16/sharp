<template>
    <div class="SharpAutocomplete" :class="classes">
        <template v-if="ready">
            <Multiselect
                class="SharpAutocomplete__multiselect"
                :class="{ 'SharpAutocomplete__multiselect--hide-dropdown':hideDropdown }"
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
                :searchable="searchable"
                @search-change="updateSuggestions($event)"
                @select="handleSelect"
                @input="$emit('multiselect-input',$event)"
                @close="handleDropdownClose"
                @open="handleDropdownOpen"
                ref="multiselect"
            >
                <template slot="clear">
                    <template v-if="clearButtonVisible">
                        <button class="SharpAutocomplete__result-item__close-button" type="button" @click="handleClearButtonClicked">
                            <svg class="SharpAutocomplete__result-item__close-icon"
                                aria-label="close" width="10" height="10" viewBox="0 0 10 10" fill-rule="evenodd">
                                <path d="M9.8 8.6L8.4 10 5 6.4 1.4 10 0 8.6 3.6 5 .1 1.4 1.5 0 5 3.6 8.6 0 10 1.4 6.4 5z"></path>
                            </svg>
                        </button>
                    </template>
                </template>
                <template slot="singleLabel" slot-scope="{ option }">
                    <TemplateRenderer
                        name="ResultItem"
                        :template="resultItemTemplate"
                        :template-data="localizedTemplateData(option)"
                    />
                </template>
                <template slot="option" slot-scope="{ option }">
                    <TemplateRenderer
                        name="ListItem"
                        :template="listItemTemplate"
                        :template-data="localizedTemplateData(option)"
                    />
                </template>
                <template slot="loading">
                    <Loading :visible="isLoading" inline small />
                </template>
                <template slot="noResult">
                    {{ l('form.autocomplete.no_results_text') }}
                </template>
            </multiselect>

            <template v-if="overlayVisible">
                <div class="SharpAutocomplete__overlay multiselect">
                    <div class="multiselect__tags">
                        <TemplateRenderer
                            name="ResultItem"
                            :template="resultItemTemplate"
                            :template-data="localizedTemplateData(value)"
                        />
                    </div>
                </div>
            </template>
        </template>
    </div>
</template>

<script>

    import debounce from 'lodash/debounce';
    import Multiselect from 'vue-multiselect';
    import { warn, lang, search } from 'sharp';
    import { TemplateRenderer } from 'sharp/components';
    import { Loading } from 'sharp-ui';
    import { Localization, Debounce } from 'sharp/mixins';

    import { getAutocompleteSuggestions } from "../../api";
    import localize from '../../mixins/localize/Autocomplete';
    import { setDefaultValue } from "../../util";


    export default {
        name:'SharpAutocomplete',
        components: {
            Multiselect,
            TemplateRenderer,
            Loading,
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
            remoteMethod: String,
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
            searchable: {
                type: Boolean,
                default: true,
            },
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
            },
            dynamicAttributes: Array,
        },
        data() {
            return {
                ready: false,
                query: '',
                suggestions: this.localValues,
                opened: false,
                isLoading: false,
            }
        },
        watch: {
            localValues() {
                if(!this.isRemote) {
                    this.updateLocalSuggestions(this.query);
                }
            },
        },
        computed: {
            isRemote() {
                return this.mode === 'remote';
            },
            hideDropdown() {
                return this.isQueryTooShort;
            },
            isQueryTooShort() {
                return this.isRemote && this.query.length < this.searchMinChars;
            },
            clearButtonVisible() {
                return !!this.value && !this.opened;
            },
            classes() {
                return [
                    { 'SharpAutocomplete--remote': this.isRemote },
                    { 'SharpAutocomplete--disabled': this.readOnly }
                ];
            },
            overlayVisible() {
                const isFormField = !!this.fieldKey;
                return this.value && isFormField;
            }
        },
        methods: {
            updateSuggestions(query) {
                this.query = query;
                if(this.isQueryTooShort) {
                    return;
                }
                if(this.isRemote) {
                    this.isLoading = true;
                    this.updateRemoteSuggestions(query);
                }
                else {
                    this.updateLocalSuggestions(query);
                }
            },

            updateLocalSuggestions(query) {
                this.suggestions = query.length >= this.searchMinChars
                    ? search(this.localValues, query, { searchKeys: this.searchKeys })
                    : this.localValues;
            },
            updateRemoteSuggestions: debounce(function(query) {
                return getAutocompleteSuggestions({
                    url: this.remoteEndpoint,
                    method: this.remoteMethod,
                    locale: this.locale,
                    searchAttribute: this.remoteSearchAttribute,
                    query,
                })
                .then(suggestions => {
                    this.suggestions = suggestions;
                })
                .finally(() => {
                    this.isLoading = false;
                });
            }, 200),

            handleSelect(value) {
                this.$emit('input', value);
            },
            handleDropdownClose() {
                this.opened = false;
                this.$emit('close');
            },
            handleDropdownOpen() {
                this.opened = true;
                this.$emit('open');
            },
            handleClearButtonClicked() {
                this.$emit('input', null);
                this.$nextTick(() => {
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
            },
            async setDefault() {
                this.$emit('input', this.findLocalValue(), { force: true });
                await this.$nextTick();
                this.ready = true;
            }
        },
        created() {
            if(this.mode === 'local' && !this.searchKeys) {
                warn(`Autocomplete (key: ${this.fieldKey}) has local mode but no searchKeys, default set to ['value']`);
            }
            if(this.isRemote) {
                this.ready = true;
            } else {
                setDefaultValue(this, this.setDefault, {
                    dependantAttributes: ['localValues'],
                });
            }
        }
    }
</script>
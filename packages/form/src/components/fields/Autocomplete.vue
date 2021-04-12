<template>
    <div class="SharpAutocomplete" :class="classes">
        <template v-if="overlayVisible">
            <div class="form-control clearable SharpAutocomplete__result">
                <TemplateRenderer
                    name="ResultItem"
                    :template="resultItemTemplate"
                    :template-data="localizedTemplateData(value)"
                    :template-props="searchKeys"
                />

                <ClearButton @click="handleClearButtonClicked" />
            </div>
        </template>
        <template v-else-if="ready">
            <Multiselect
                class="SharpAutocomplete__multiselect form-control"
                :class="{
                    'form-select': !this.isRemote,
                    'SharpAutocomplete__multiselect--hide-dropdown': hideDropdown,
                }"
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
                :readonly="readOnly"
                :tabindex="readOnly ? -1 : 0"
                @search-change="updateSuggestions($event)"
                @select="handleSelect"
                @input="$emit('multiselect-input',$event)"
                @close="handleDropdownClose"
                @open="handleDropdownOpen"
                ref="multiselect"
            >
                <template v-slot:clear>
                    <template v-if="clearButtonVisible">
                        <ClearButton @click="handleClearButtonClicked" />
                    </template>
                </template>
                <template v-slot:singleLabel="{ option }">
                    <TemplateRenderer
                        name="ResultItem"
                        :template="resultItemTemplate"
                        :template-data="localizedTemplateData(option)"
                        :template-props="searchKeys"
                    />
                </template>
                <template v-slot:option="{ option }">
                    <TemplateRenderer
                        name="ListItem"
                        :template="listItemTemplate"
                        :template-data="localizedTemplateData(option)"
                        :template-props="searchKeys"
                    />
                </template>
                <template v-slot:loading>
                    <Loading :visible="isLoading" small />
                </template>
                <template v-slot:noResult>
                    {{ l('form.autocomplete.no_results_text') }}
                </template>
            </multiselect>
        </template>
    </div>
</template>

<script>
    import debounce from 'lodash/debounce';
    import Multiselect from 'vue-multiselect';
    import { CancelToken } from 'axios';
    import { warn, lang, search, logError } from 'sharp';
    import { TemplateRenderer } from 'sharp/components';
    import { Loading, ClearButton,  multiselectUpdateScroll } from 'sharp-ui';
    import { Localization } from 'sharp/mixins';

    import { getAutocompleteSuggestions } from "../../api";
    import localize from '../../mixins/localize/Autocomplete';
    import { setDefaultValue } from "../../util";


    export default {
        name: 'SharpAutocomplete',
        components: {
            Multiselect,
            TemplateRenderer,
            Loading,
            ClearButton,
        },

        mixins: [Localization, localize],

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
            dataWrapper: String,
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
            debounceDelay: {
                type: Number,
                default: 400,
            },
            nowrap: Boolean,
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
                return {
                    'SharpAutocomplete--remote': this.isRemote,
                    'SharpAutocomplete--disabled': this.readOnly,
                    'SharpAutocomplete--wrap': !this.nowrap,
                };
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
            updateRemoteSuggestions(query) {
                this.cancelSource?.cancel();
                this.cancelSource = CancelToken.source();
                return getAutocompleteSuggestions({
                    url: this.remoteEndpoint,
                    method: this.remoteMethod,
                    locale: this.locale,
                    searchAttribute: this.remoteSearchAttribute,
                    dataWrapper: this.dataWrapper,
                    fieldKey: this.fieldKey,
                    query,
                    cancelToken: this.cancelSource.token,
                })
                .then(suggestions => {
                    this.suggestions = suggestions;
                    this.scroll();
                })
                .finally(() => {
                    this.isLoading = false;
                });
            },
            scroll() {
               multiselectUpdateScroll(this);
            },
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
                this.scroll();
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
                    logError(`Autocomplete (key: ${this.fieldKey}) can't find local value matching : ${JSON.stringify(this.value)}`);
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
            this.updateRemoteSuggestions = debounce(this.updateRemoteSuggestions, this.debounceDelay);

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

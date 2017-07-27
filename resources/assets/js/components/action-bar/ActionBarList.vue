<template>
    <sharp-action-bar>
        <template slot="left">
            <span>{{ itemsCount }} {{ l('action_bar.list.items_count') }}</span>
            <sharp-filter-select v-for="filter in filters" :name="filter.key" :values="filter.values"
                                 :key="filter.key" @input="emitAction('filterChanged',filter.key,$event)"
                                 :value="filtersValue[filter.key]" :multiple="filter.multiple">
            </sharp-filter-select>
        </template>
        <template slot="right">
            <div class="SharpActionBar__search SharpSearch SharpSearch--lg" role="search">
                <svg class="SharpSearch__magnifier" width="16" height="16" viewBox="0 0 16 16" fill-rule="evenodd">
                    <path d="M6 2c2.2 0 4 1.8 4 4s-1.8 4-4 4-4-1.8-4-4 1.8-4 4-4zm0-2C2.7 0 0 2.7 0 6s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6zM16 13.8L13.8 16l-3.6-3.6 2.2-2.2z"></path>
                    <path d="M16 13.8L13.8 16l-3.6-3.6 2.2-2.2z"></path>
                </svg>
                <label id="ab-search-label" class="SharpSearch__label" for="ab-search-input">{{ l('action_bar.list.search.placeholder') }}</label>
                <input class="SharpSearch__input" type="text" id="ab-search-input" role="search" :placeholder="l('action_bar.list.search.placeholder')"
                       aria-labelledby="ab-search-label" @keyup.enter="emitSearch"
                       @input="search=$event.target.value" :value="search" ref="search">
                <svg class="SharpSearch__close" :class="{'SharpSearch__close--hidden':!(search||'').length}"
                     @click="closeClicked"
                     width="16" height="16" viewBox="0 0 16 16" fill-rule="evenodd">
                    <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z"></path>
                </svg>
                <button class="SharpSearch__button" type="button" aria-label="Search button" @click="emitSearch">
                    <svg width="16" height="14" viewBox="0 0 16 14" fill-rule="evenodd"><path d="M12.6 6H0v2h12.7l-5 4.7L9 14l7-7-7-7-1.3 1.3z"></path></svg>
                </button>
            </div>
        </template>
    </sharp-action-bar>
</template>

<script>
    import ActionBar from './ActionBar';
    import ActionBarMixin from './ActionBarMixin';
    import { Localization, ActionEvents } from '../../mixins';

    import Text from '../form/fields/Text';

    import FilterSelect from '../list/FilterSelect';

    export default {
        name: 'SharpActionBarList',
        components : {
            [ActionBar.name]: ActionBar,
            [Text.name]: Text,
            [FilterSelect.name]: FilterSelect
        },

        mixins: [ActionBarMixin, ActionEvents, Localization],

        data() {
            return {
                itemsCount: 0,
                search:'',
                filters: [],
                filtersValue: {}
            }
        },
        methods: {
            closeClicked() {
                this.actionsBus.$emit('searchChanged','',{ isInput:false });
                this.$refs.search.focus();
            },
            emitSearch() {
                this.actionsBus.$emit('searchChanged',this.search);
            }
        },
        actions: {
            setup(config) {
                let { itemsCount, filters, filtersValue } = config;
                this.itemsCount = itemsCount;
                this.filters = filters;
                this.filtersValue = filtersValue;
            },
            searchChanged(input) {
                this.search = input;
            },
            filterChanged(key, value) {
                this.filtersValue[key] = value;
            }
        }
    }
</script>
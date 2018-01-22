<template>
    <sharp-action-bar class="SharpActionBarList" :class="{'SharpActionBarList--search-active':searchActive}" :ready="ready">

        <template slot="left">
            <span>{{ itemsCount }} {{ l('action_bar.list.items_count') }}</span>
        </template>
        <template slot="right">
            <div v-if="searchable && !reorderActive" class="SharpActionBar__search SharpSearch SharpSearch--lg" :class="{'SharpSearch--active':searchActive}" role="search">
                <label id="ab-search-label" class="SharpSearch__label" for="ab-search-input">{{ l('action_bar.list.search.placeholder') }}</label>
                <input class="SharpSearch__input"
                       :value="search"
                       :placeholder="l('action_bar.list.search.placeholder')"
                       type="text"
                       id="ab-search-input"
                       role="search"
                       aria-labelledby="ab-search-label"
                       @keyup.enter="emitSearch"
                       @focus="searchActive=true"
                       @blur="searchActive=false"
                       @input="search=$event.target.value"
                       ref="search">
                <svg class="SharpSearch__magnifier" width="16" height="16" viewBox="0 0 16 16" fill-rule="evenodd">
                    <path d="M6 2c2.2 0 4 1.8 4 4s-1.8 4-4 4-4-1.8-4-4 1.8-4 4-4zm0-2C2.7 0 0 2.7 0 6s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6zM16 13.8L13.8 16l-3.6-3.6 2.2-2.2z"></path>
                    <path d="M16 13.8L13.8 16l-3.6-3.6 2.2-2.2z"></path>
                </svg>
                <svg class="SharpSearch__close" :class="{'SharpSearch__close--hidden':!(search||'').length}"
                     @click="closeClicked"
                     width="16" height="16" viewBox="0 0 16 16" fill-rule="evenodd">
                    <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z"></path>
                </svg>
            </div>
            <template v-if="showReorderButton">
                <template v-if="reorderActive">
                    <button class="SharpButton SharpButton--secondary-accent" @click="emitAction('toggleReorder')">
                        {{ l('action_bar.list.reorder_button.cancel') }}
                    </button>
                    <button class="SharpButton SharpButton--accent" @click="emitAction('toggleReorder', { apply: true })">
                        {{ l('action_bar.list.reorder_button.finish') }}
                    </button>
                </template>
                <template v-else>
                    <button class="SharpButton SharpButton--secondary-accent" @click="emitAction('toggleReorder')">
                        {{ l('action_bar.list.reorder_button') }}
                    </button>
                </template>
            </template>

            <template v-if="!reorderActive">
                <template v-if="showCreateButton">
                    <sharp-dropdown v-if="hasForms" class="SharpActionBar__forms-dropdown" :text="l('action_bar.list.create_button')">
                        <sharp-dropdown-item v-for="(form,key) in forms" @click="emitAction('create', form)" :key="key" >
                            {{ form.label }}
                        </sharp-dropdown-item>
                    </sharp-dropdown>
                    <button v-else class="SharpButton SharpButton--accent" @click="emitAction('create')">
                        {{ l('action_bar.list.create_button') }}
                    </button>
                </template>
                <sharp-dropdown v-if="commands.length"
                                class="SharpActionBar__actions-dropdown SharpActionBar__actions-dropdown--commands"
                                :show-arrow="false">
                    <div slot="text">
                        <i class="fa fa-plus"></i>
                    </div>
                    <sharp-dropdown-item v-for="command in commands" @click="emitAction('command', command)" :key="command.key">
                        {{ command.label }}
                    </sharp-dropdown-item>
                </sharp-dropdown>
            </template>
        </template>
        <template slot="extras">
            <sharp-filter-select v-for="filter in filters"
                                 v-show="!reorderActive"
                                 :name="filter.label"
                                 :filter-key="`actionbarlist_${filter.key}`"
                                 :values="filter.values"
                                 :value="filtersValue[filter.key]"
                                 :multiple="filter.multiple"
                                 :required="filter.required"
                                 :key="filter.key"
                                 @input="emitAction('filterChanged',filter.key,$event)">
            </sharp-filter-select>
        </template>
    </sharp-action-bar>
</template>

<script>
    import ActionBar from './ActionBar';
    import ActionBarMixin from './ActionBarMixin';
    import { Localization, ActionEvents } from '../../mixins';

    import Text from '../form/fields/Text';

    import FilterSelect from '../list/FilterSelect';

    import Dropdown from '../dropdown/Dropdown';
    import DropdownItem from '../dropdown/DropdownItem';

    export default {
        name: 'SharpActionBarList',
        components : {
            [ActionBar.name]: ActionBar,
            [Text.name]: Text,
            [FilterSelect.name]: FilterSelect,
            [Dropdown.name]: Dropdown,
            [DropdownItem.name]: DropdownItem
        },

        mixins: [ActionBarMixin, ActionEvents, Localization],

        data() {
            return {
                itemsCount: 0,
                search:'',
                filters: [],
                filtersValue: {},
                commands: [],
                forms: {},

                showCreateButton: false,
                showReorderButton: false,
                searchActive: false,
                searchable: false,

                reorderActive: false
            }
        },
        computed: {
            hasForms() {
                return this.forms && Object.keys(this.forms).length > 0;
            }
        },
        methods: {
            closeClicked() {
                this.actionsBus.$emit('searchChanged','',{ isInput:false });
                this.$refs.search.focus();
            },
            emitSearch() {
                this.actionsBus.$emit('searchChanged',this.search);
            },
            handleSearchFocus() {
                this.searchActive = true;
            }
        },
        actions: {
            setup(config) {
                let { itemsCount, filters, filtersValue, commands, showCreateButton, showReorderButton, searchable, forms } = config;
                this.itemsCount = itemsCount;
                this.filters = filters;
                this.filtersValue = filtersValue;
                this.commands = commands;
                this.forms = forms;

                this.showCreateButton = showCreateButton;
                this.showReorderButton = showReorderButton;
                this.searchable = searchable;
            },
            searchChanged(input) {
                this.search = input;
            },
            filterChanged(key, value) {
                this.$set(this.filtersValue,key,value);
            },

            toggleReorder() {
                this.reorderActive = !this.reorderActive;
                document.activeElement.blur();
            }
        },
        mounted() {
            //console.log(this);
        }
    }
</script>
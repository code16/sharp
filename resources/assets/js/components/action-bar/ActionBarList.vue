<template>
    <sharp-action-bar class="SharpActionBarList" :class="{'SharpActionBarList--search-active':searchActive}">
        <template slot="left">
            <span class="text-content">{{ count }} {{ l('action_bar.list.items_count') }}</span>
        </template>
        <template slot="right">
            <div v-if="canSearch && !reorderActive" class="SharpActionBar__search SharpSearch SharpSearch--lg" :class="{'SharpSearch--active':searchActive}" role="search">
                <form @submit.prevent="handleSearchSubmitted">
                    <label id="ab-search-label" class="SharpSearch__label" for="ab-search-input">{{ l('action_bar.list.search.placeholder') }}</label>
                    <input class="SharpSearch__input"
                        :value="search"
                        :placeholder="l('action_bar.list.search.placeholder')"
                        type="text"
                        id="ab-search-input"
                        role="search"
                        aria-labelledby="ab-search-label"
                        @input="handleSearchInput"
                        @focus="handleSearchFocused"
                        @blur="handleSearchBlur"
                        ref="search"
                    >
                    <svg class="SharpSearch__magnifier" width="16" height="16" viewBox="0 0 16 16" fill-rule="evenodd">
                        <path d="M6 2c2.2 0 4 1.8 4 4s-1.8 4-4 4-4-1.8-4-4 1.8-4 4-4zm0-2C2.7 0 0 2.7 0 6s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6zM16 13.8L13.8 16l-3.6-3.6 2.2-2.2z"></path>
                        <path d="M16 13.8L13.8 16l-3.6-3.6 2.2-2.2z"></path>
                    </svg>
                    <svg class="SharpSearch__close" :class="{'SharpSearch__close--hidden':!(search||'').length}"
                        @click="handleClearButtonClicked"
                        width="16" height="16" viewBox="0 0 16 16" fill-rule="evenodd">
                        <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z"></path>
                    </svg>
                </form>
            </div>
            <template v-if="canReorder">
                <template v-if="reorderActive">
                    <button class="SharpButton SharpButton--secondary-accent" @click="handleReorderButtonClicked">
                        {{ l('action_bar.list.reorder_button.cancel') }}
                    </button>
                    <button class="SharpButton SharpButton--accent" @click="handleReorderSubmitButtonClicked">
                        {{ l('action_bar.list.reorder_button.finish') }}
                    </button>
                </template>
                <template v-else>
                    <button class="SharpButton SharpButton--secondary-accent" @click="handleReorderButtonClicked">
                        {{ l('action_bar.list.reorder_button') }}
                    </button>
                </template>
            </template>

            <template v-if="!reorderActive">
                <template v-if="canCreate">
                    <sharp-dropdown v-if="hasForms" class="SharpActionBar__forms-dropdown" :text="l('action_bar.list.forms_dropdown')">
                        <sharp-dropdown-item v-for="(form,key) in forms" @click="handleCreateFormSelected(form)" :key="key" >
                            <sharp-item-visual :item="form" icon-class="fa-fw"/>{{ form.label }}
                        </sharp-dropdown-item>
                    </sharp-dropdown>
                    <button v-else class="SharpButton SharpButton--accent" @click="handleCreateButtonClicked">
                        {{ l('action_bar.list.create_button') }}
                    </button>
                </template>
            </template>
        </template>
        <template slot="extras">
            <sharp-filter-select
                v-for="filter in filters"
                v-show="!reorderActive"
                :name="filter.label"
                :filter-key="`actionbarlist_${filter.key}`"
                :values="filter.values"
                :value="filtersValues[filter.key]"
                :multiple="filter.multiple"
                :required="filter.required"
                :template="filter.template"
                :search-keys="filter.searchKeys"
                :searchable="filter.searchable"
                :key="filter.key"
                @input="handleFilterChanged(filter, $event)"
            />
        </template>
        <template v-if="commands.length" slot="extras-right">
            <SharpCommandsDropdown class="SharpActionBar__actions-dropdown SharpActionBar__actions-dropdown--commands"
                :commands="commands"
                @select="handleCommandSelected"
            >
                <div slot="text">
                    {{ l('entity_list.commands.entity.label') }}
                </div>
            </SharpCommandsDropdown>
        </template>
    </sharp-action-bar>
</template>

<script>
    import SharpActionBar from './ActionBar';
    import { Localization } from '../../mixins';

    import SharpText from '../form/fields/Text';
    import SharpFilterSelect from '../list/FilterSelect';

    import SharpDropdown from '../dropdown/Dropdown';
    import SharpDropdownItem from '../dropdown/DropdownItem';
    import SharpItemVisual from '../ui/ItemVisual';
    import SharpCommandsDropdown from '../commands/CommandsDropdown';

    export default {
        name: 'SharpActionBarList',
        components : {
            SharpActionBar,
            SharpText,
            SharpFilterSelect,
            SharpDropdown,
            SharpDropdownItem,
            SharpItemVisual,
            SharpCommandsDropdown
        },

        mixins: [Localization],

        props: {
            count: Number,
            search: String,
            filters: Array,
            filtersValues: Object,
            commands: Array,
            forms: Array,

            canCreate: Boolean,
            canReorder: Boolean,
            canSearch: Boolean,

            reorderActive: Boolean
        },

        data() {
            return {
                searchActive: false
            }
        },
        computed: {
            hasForms() {
                return this.forms && this.forms.length > 0;
            }
        },
        methods: {
            handleSearchFocused() {
                this.searchActive = true;
            },
            handleSearchBlur() {
                this.searchActive = false;
            },
            handleSearchInput(e) {
                this.$emit('search-change', e.target.value);
            },
            handleClearButtonClicked() {
                this.$emit('search-change', '');
                this.$refs.search.focus();
            },
            handleSearchSubmitted() {
                this.$emit('search-submit');
            },
            handleFilterChanged(filter, value) {
                this.$emit('filter-change', filter, value);
            },
            handleReorderButtonClicked() {
                this.$emit('reorder-click');
                document.activeElement.blur();
            },
            handleReorderSubmitButtonClicked() {
                this.$emit('reorder-submit');
            },
            handleCommandSelected(command) {
                this.$emit('command', command);
            },
            handleCreateButtonClicked() {
                this.$emit('create');
            },
            handleCreateFormSelected(form) {
                this.$emit('create', form);
            }
        }
    }
</script>
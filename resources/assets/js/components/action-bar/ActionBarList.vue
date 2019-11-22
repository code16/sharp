<template>
    <sharp-action-bar class="SharpActionBarList" :class="{'SharpActionBarList--search-active':searchActive}">
        <template slot="left">
            <span class="text-content text-nowrap">{{ count }} {{ l('action_bar.list.items_count') }}</span>
        </template>
        <template slot="right">
            <template v-if="canSearch && !reorderActive">
                <SharpSearch
                    :value="search"
                    :active.sync="searchActive"
                    :placeholder="l('action_bar.list.search.placeholder')"
                    @input="handleSearchInput"
                    @submit="handleSearchSubmitted"
                />
            </template>

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
        <template v-if="!reorderActive">
            <template slot="extras">
                <div class="row mx-n2">
                    <template v-for="filter in filters">
                        <div class="col-auto px-2">
                            <SharpFilter
                                :filter="filter"
                                :value="filtersValues[filter.key]"
                                @input="handleFilterChanged(filter, $event)"
                                :key="filter.id"
                            />
                        </div>
                    </template>
                </div>
            </template>

            <template slot="extras-right">
                <template v-if="hasCommands">
                    <SharpCommandsDropdown class="SharpActionBar__actions-dropdown SharpActionBar__actions-dropdown--commands"
                        :commands="commands"
                        @select="handleCommandSelected"
                    >
                        <div slot="text">
                            {{ l('entity_list.commands.entity.label') }}
                        </div>
                    </SharpCommandsDropdown>
                </template>
            </template>
        </template>
    </sharp-action-bar>
</template>

<script>
    import SharpActionBar from './ActionBar';
    import { Localization } from '../../mixins';

    import SharpFilter from '../list/Filter';

    import SharpDropdown from '../dropdown/Dropdown';
    import SharpDropdownItem from '../dropdown/DropdownItem';
    import SharpItemVisual from '../ui/ItemVisual';
    import SharpCommandsDropdown from '../commands/CommandsDropdown';
    import SharpSearch from '../ui/Search';

    export default {
        name: 'SharpActionBarList',

        mixins: [Localization],

        components : {
            SharpActionBar,
            SharpDropdown,
            SharpDropdownItem,
            SharpItemVisual,
            SharpCommandsDropdown,
            SharpFilter,
            SharpSearch,
        },

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
            },
            hasCommands() {
                return this.commands && this.commands.some(group => group && group.length > 0);
            },
        },
        methods: {
            handleSearchInput(search) {
                this.$emit('search-change', search);
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
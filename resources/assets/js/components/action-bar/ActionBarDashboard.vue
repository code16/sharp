<template>
    <SharpActionBar>
        <template slot="extras">
            <SharpFilterSelect
                v-for="filter in filters"
                :name="filter.label"
                :values="filter.values"
                :value="filterValue(filter.key)"
                :filter-key="filterKey(filter)"
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
                    {{ l('dashboard.commands.dashboard.label') }}
                </div>
            </SharpCommandsDropdown>
        </template>
    </SharpActionBar>
</template>

<script>
    import SharpActionBar from './ActionBar.vue';
    import SharpFilterSelect from '../list/FilterSelect.vue';
    import SharpCommandsDropdown from '../commands/CommandsDropdown.vue';
    import { Localization } from "../../mixins";
    import { mapGetters } from 'vuex';

    export default {
        name: 'SharpActionBarDashboard',
        mixins: [Localization],
        components: {
            SharpActionBar,
            SharpFilterSelect,
            SharpCommandsDropdown,
        },
        props: {
            commands: Array,
        },
        computed: {
            ...mapGetters('dashboard', {
                filters: 'filters/filters',
                filterValue: 'filters/value',
                filterNextQuery: 'filters/nextQuery',
            })
        },
        methods: {
            filterKey(filter) {
                return `actionbardashboard_${filter.key}`;
            },
            handleFilterChanged(filter, value) {
                this.$router.push({
                    query: {
                        ...this.$route.query,
                        ...this.filterNextQuery({ filter, value }),
                    }
                });
            },
            handleCommandSelected(command) {
                this.$emit('command', command);
            }
        }
    }
</script>
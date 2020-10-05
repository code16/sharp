<template>
    <ActionBar>
        <template v-slot:extras>
            <div class="row mx-n2">
                <template v-for="filter in filters">
                    <div class="col-auto px-2">
                        <FilterDropdown
                            :filter="filter"
                            :value="filterValue(filter.key)"
                            @input="handleFilterChanged(filter, $event)"
                            :key="filter.id"
                        />
                    </div>
                </template>
            </div>
        </template>
        <template v-if="commands.length" v-slot:extras-right>
            <CommandsDropdown class="SharpActionBar__actions-dropdown SharpActionBar__actions-dropdown--commands"
                :commands="commands"
                @select="handleCommandSelected"
            >
                <template v-slot:text>
                    {{ l('dashboard.commands.dashboard.label') }}
                </template>
            </CommandsDropdown>
        </template>
    </ActionBar>
</template>

<script>
    import { mapGetters } from 'vuex';
    import { ActionBar } from 'sharp-ui';
    import { FilterDropdown } from 'sharp-filters';
    import { CommandsDropdown } from 'sharp-commands';
    import { Localization } from "sharp/mixins";

    export default {
        name: 'SharpActionBarDashboard',
        mixins: [Localization],
        components: {
            ActionBar,
            FilterDropdown,
            CommandsDropdown,
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
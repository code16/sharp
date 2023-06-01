<template>
    <div class="mb-4">
        <div class="row gx-3">
            <div class="col">
                <template v-if="filters.length > 0">
                    <div class="row mx-n2">
                        <template v-for="filter in filters">
                            <div class="col-auto px-2">
                                <SharpFilter
                                    :filter="filter"
                                    :value="filterValue(filter.key)"
                                    @input="$emit('filter-change', filter, $event)"
                                    :key="filter.id"
                                />
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            <template v-if="commands.length">
                <div class="col-auto">
                    <CommandsDropdown :commands="commands" @select="$emit('command', $event)">
                        <template v-slot:text>
                            {{ l('dashboard.commands.dashboard.label') }}
                        </template>
                    </CommandsDropdown>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import { SharpFilter } from 'sharp-filters';
    import { CommandsDropdown } from 'sharp-commands';
    import { Localization } from "sharp/mixins";

    export default {
        name: 'SharpActionBarDashboard',
        mixins: [Localization],
        components: {
            SharpFilter,
            CommandsDropdown,
        },
        props: {
            commands: Array,
            filters: Array,
        },
        computed: {
            ...mapGetters('dashboard', {
                filterValue: 'filters/value',
            })
        },
    }
</script>

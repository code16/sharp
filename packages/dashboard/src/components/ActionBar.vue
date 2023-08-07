<script setup lang="ts">
    import { __ } from "@/utils/i18n";
</script>

<template>
    <div class="my-4">
        <div class="row gx-3">
            <div class="col">
                <template v-if="filters.length > 0">
                    <div class="row gx-2">
                        <template v-for="filter in filters" :key="filter.id">
                            <div class="col-auto">
                                <SharpFilter
                                    :filter="filter"
                                    :value="filterValue(filter.key)"
                                    @input="$emit('filter-change', filter, $event)"
                                />
                            </div>
                        </template>
                        <template v-if="showReset">
                            <div class="col-auto d-flex">
                                <button class="btn btn-sm btn-link d-inline-flex align-items-center fs-8" @click="$emit('filters-reset', filters)">
                                    {{ __('sharp::filters.reset_all') }}
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            <template v-if="commands.length">
                <div class="col-auto">
                    <CommandsDropdown :commands="commands" @select="$emit('command', $event)">
                        <template v-slot:text>
                            {{ __('sharp::dashboard.commands.dashboard.label') }}
                        </template>
                    </CommandsDropdown>
                </div>
            </template>
        </div>
    </div>
</template>

<script lang="ts">
    import { mapGetters } from 'vuex';
    import { SharpFilter } from '@sharp/filters';
    import { CommandsDropdown } from '@sharp/commands';

    export default {
        name: 'SharpActionBarDashboard',
        components: {
            SharpFilter,
            CommandsDropdown,
        },
        props: {
            commands: Array,
            filters: Array,
            showReset: Boolean,
        },
        computed: {
            ...mapGetters('dashboard', {
                filterValue: 'filters/value',
            })
        },
    }
</script>

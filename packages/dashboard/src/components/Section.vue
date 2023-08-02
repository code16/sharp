<script setup lang="ts">
    import { __ } from "@/util/i18n";
</script>

<template>
    <div class="section">
        <div class="row align-items-center">
            <div class="col">
                <SectionTitle
                    :section="section"
                />
            </div>
            <template v-if="hasCommands || filters.length">
                <div class="col-auto align-self-end mb-2" :class="{ 'w-100': filters.length }">
                    <div class="row justify-content-end">
                        <template v-if="filters.length">
                            <div class="col">
                                <div class="row row-cols-auto gx-2">
                                    <template v-for="filter in filters" :key="filter.id">
                                        <SharpFilter
                                            :filter="filter"
                                            :value="filterValue(filter.key)"
                                            @input="$emit('filter-change', filter, $event)"
                                        />
                                    </template>
                                    <template v-if="showReset">
                                        <div class="d-flex">
                                            <button class="btn btn-sm d-inline-flex align-items-center btn-link" @click="$emit('filters-reset', filters)">
                                                {{ __('sharp::filters.reset_all') }}
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template v-if="hasCommands">
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
        </div>

        <Grid :rows="section.rows" row-class="g-3" v-slot="{ itemLayout }">
            <slot :widget-layout="itemLayout" />
        </Grid>
    </div>
</template>

<script lang="ts">
    import {Grid, SectionTitle} from "@sharp/ui";
    import {SharpFilter} from "@sharp/filters";
    import {mapGetters} from "vuex";
    import {CommandsDropdown} from "@sharp/commands";
    import Widget from "./Widget.vue";

    export default {
        components: {
            CommandsDropdown,
            Widget,
            Grid,
            SectionTitle,
            SharpFilter,
        },
        props: {
            section: Object,
            commands: Array,
            filters: Array,
            showReset: Boolean,
        },
        computed: {
            ...mapGetters('dashboard', {
                filterValue: 'filters/value',
            }),
            hasCommands() {
                return this.commands?.flat().length;
            },
        },
    }
</script>

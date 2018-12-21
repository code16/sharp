<template>
    <sharp-action-bar :ready="ready">
        <template slot="extras">
            <sharp-filter-select
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
    </sharp-action-bar>
</template>

<script>
    import SharpActionBar from './ActionBar';
    import SharpFilterSelect from '../list/FilterSelect';

    import { mapState, mapGetters } from 'vuex';

    export default {
        name: 'SharpActionBarDashboard',
        components: {
            SharpActionBar,
            SharpFilterSelect
        },
        computed: {
            ...mapState('dashboard', {
                ready: state => state.ready
            }),
            ...mapGetters('dashboard', {
                filters: 'filters/filters',
                filterValue: 'filters/value',
                filterNextQuery: 'filters/nextQuery'
            }),
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
            }
        }
    }
</script>
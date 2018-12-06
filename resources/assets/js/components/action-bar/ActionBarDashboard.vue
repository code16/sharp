<template>
    <sharp-action-bar ready>
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
    import ActionBarMixin from './ActionBarMixin';
    import SharpFilterSelect from '../list/FilterSelect';

    import { mapGetters } from 'vuex';

    export default {
        name: 'SharpActionBarDashboard',
        mixins: [ActionBarMixin],
        components: {
            SharpActionBar,
            SharpFilterSelect
        },
        computed: {
            ...mapGetters('dashboard', {
                filters: 'filters/filters',
                filterValue: 'filters/value',
                filterQueryKey: 'filters/filterQueryKey'
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
                        [this.filterQueryKey(filter.key)]: value
                    }
                });
            }
        }
    }
</script>
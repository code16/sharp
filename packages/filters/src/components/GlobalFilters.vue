<template>
    <div class="SharpGlobalFilters">
        <template v-for="filter in filters">
            <FilterSelect
                :label="null"
                :values="filter.values"
                :value="filterValue(filter.key)"
                :multiple="filter.multiple"
                :required="filter.required"
                :template="filter.template"
                :search-keys="filter.searchKeys"
                :searchable="filter.searchable"
                :key="filter.key"
                form-select
                @input="handleFilterChanged(filter, $event)"
                @open="handleOpened(filter)"
                @close="handleClosed(filter)"
            />
        </template>
    </div>
</template>

<script>
    import debounce from 'lodash/debounce';
    import { mapGetters } from 'vuex';
    import { BASE_URL } from "sharp";
    import FilterSelect from './filters/FilterSelect';

    export default {
        components: {
            FilterSelect
        },
        computed: {
            ...mapGetters('global-filters', {
                filters: 'filters/filters',
                filterValue: 'filters/value',
            }),
        },
        methods: {
            handleFilterChanged(filter, value) {
                this.$store.dispatch('global-filters/post', { filter, value })
                    .then(() => {
                        this.$store.dispatch('setLoading', true);
                        location.href = BASE_URL;
                    });
            },
            handleOpened() {
                this.$emit('open');
            },
            handleClosed() {
                this.$emit('close');
            },
        },
    }
</script>

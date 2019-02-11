<template>
    <div class="SharpGlobalFilters">
        <template v-for="filter in filters">
            <SharpFilterSelect
                :name="filter.label"
                :values="filter.values"
                :value="filterValue(filter.key)"
                :multiple="filter.multiple"
                :required="filter.required"
                :template="filter.template"
                :search-keys="filter.searchKeys"
                :searchable="filter.searchable"
                :key="filter.key"
                @input="handleFilterChanged(filter, $event)"
            />
        </template>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import SharpFilterSelect from '../list/FilterSelect.vue';
    import { BASE_URL } from "../../consts";

    export default {
        components: {
            SharpFilterSelect
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
                        location.href = BASE_URL;
                    });
            },
        },
        created() {
            this.$store.dispatch('global-filters/get');
        }
    }
</script>
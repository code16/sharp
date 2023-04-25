<template>
    <div class="SharpGlobalFilters">
        <template v-if="open">
            <div class="position-absolute inset-0" style="z-index: 1">
            </div>
        </template>
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
                @input="handleFilterChanged(filter, $event)"
                @open="handleOpened(filter)"
                @close="handleClosed(filter)"
                style="z-index: 2"
            />
        </template>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import { BASE_URL } from "sharp";
    import FilterSelect from './filters/FilterSelect';
    import { Dropdown } from "sharp-ui";

    export default {
        components: {
            FilterSelect,
            Dropdown,
        },
        computed: {
            ...mapGetters('global-filters', {
                filters: 'filters/filters',
                filterValue: 'filters/value',
            }),
        },
        data() {
            return {
                open: false,
            }
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
                this.open = true;
            },
            handleClosed() {
                this.open = false;
            },
            async init() {
                await this.$store.dispatch('global-filters/get');
            },
        },
        created() {
            this.init();
        },
    }
</script>

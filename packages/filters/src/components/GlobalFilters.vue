<template>
    <div class="SharpGlobalFilters">
        <template v-for="filter in filters">
            <FilterSelect
                :label="filter.label"
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
            />
        </template>
    </div>
</template>

<script>
    import Vue from 'vue';
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
            updateLayout: debounce(function () {
                [...this.$el.querySelectorAll('.SharpFilterSelect')].forEach(select => {
                    const container = this.$el.parentElement;
                    const height = (container.offsetHeight-select.offsetHeight) - (select.offsetTop-container.offsetTop);
                    const dropdown = select.querySelector('.SharpAutocomplete .multiselect__content');
                    dropdown.style.height = `${height}px`;
                });
            }, 300)
        },
        mounted() {
            this.updateLayout();
            window.addEventListener('resize', this.updateLayout);
        },
        destroyed() {
            window.removeEventListener('resize', this.updateLayout);
        }
    }
</script>
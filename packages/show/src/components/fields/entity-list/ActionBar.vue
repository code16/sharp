<template>
    <div class="action-bar"
        :class="{ 'position-sticky': sticky }"
        v-sticky="sticky"
        @sticky-change="stuck = $event.detail"
    >
        <div class="position-relative">
            <template v-if="hasOuterTitle">
                <div class="mb-2">
                    <slot />
                </div>
            </template>
            <template v-if="ready && barVisible">
                <div class="row align-items-end align-content-end" data-sticky-anchor>
                    <template v-if="hasLeftControls && !stuck">
                        <div class="col-sm mb-2">
                            <div class="row gy-1 gx-2 gx-md-3">
                                <template v-for="filter in filters">
                                    <div class="col-auto mb-1">
                                        <div class="action-bar__element">
                                            <SharpFilter
                                                class="h-100"
                                                :filter="filter"
                                                :value="filtersValues[filter.key]"
                                                :disabled="reordering"
                                                @input="handleFilterChanged(filter, $event)"
                                                :key="filter.id"
                                            />
                                        </div>
                                    </div>
                                </template>
                                <template v-if="canSearch">
                                    <div class="col-auto mb-1">
                                        <div class="action-bar__element">
                                            <Search
                                                class="h-100"
                                                :value="search"
                                                :placeholder="l('action_bar.list.search.placeholder')"
                                                :disabled="reordering"
                                                @submit="handleSearchSubmitted"
                                            />
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-sm align-self-end">
                            <slot />
                        </div>
                    </template>

                    <template v-if="hasRightControls && !collapsed">
                        <div class="col-sm-auto mb-2">
                            <div class="row flex-nowrap justify-content-end g-2 gx-md-3">
                                <template v-if="canReorder">
                                    <div class="col-auto">
                                        <template v-if="reordering">
                                            <div class="row gx-3">
                                                <div class="col-auto">
                                                    <Button text small @click="handleReorderButtonClicked">
                                                        {{ l('action_bar.list.reorder_button.cancel') }}
                                                    </Button>
                                                </div>
                                                <div class="col-auto">
                                                    <Button variant="primary" small @click="handleReorderSubmitButtonClicked">
                                                        {{ l('action_bar.list.reorder_button.finish') }}
                                                    </Button>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <Button text small @click="handleReorderButtonClicked">
                                                {{ l('action_bar.list.reorder_button') }}
                                            </Button>
                                        </template>
                                    </div>
                                </template>

                                <template v-if="primaryCommand && !reordering">
                                    <div class="col-auto">
                                        <Button variant="primary" small @click="handlePrimaryCommandClicked">
                                            {{ primaryCommand.label }}
                                        </Button>
                                    </div>
                                </template>

                                <template v-if="canCreate && !reordering">
                                    <div class="col-auto">
                                        <div class="action-bar__element">
                                            <template v-if="hasForms">
                                                <MultiformDropdown
                                                    :forms="forms"
                                                    variant="primary"
                                                    right
                                                    small
                                                    @select="handleCreateFormSelected"
                                                />
                                            </template>
                                            <template v-else>
                                                <Button variant="primary" small @click="handleCreateButtonClicked">
                                                    {{ l('action_bar.list.create_button') }}
                                                </Button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    import { Localization } from 'sharp/mixins';
    import { Search,  Button } from 'sharp-ui';
    import { SharpFilter } from 'sharp-filters';
    import { MultiformDropdown } from "sharp-entity-list";
    import { lang } from "sharp";
    import { sticky } from "sharp/directives";

    export default {
        mixins: [Localization],
        components: {
            Search,
            SharpFilter,
            MultiformDropdown,
            Button,
        },
        props: {
            ready: Boolean,
            count: Number,
            search: String,
            hasSearchQuery: Boolean,
            filters: Array,
            filtersValues: Object,
            primaryCommand: Object,

            forms: Array,

            canCreate: Boolean,
            canReorder: Boolean,
            canSearch: Boolean,

            reordering: Boolean,
            selecting: Boolean,

            // show field props
            collapsed: Boolean,
            hasActiveQuery: Boolean,
            sticky: Boolean,
        },
        data() {
            return {
                stuck: false,
            }
        },
        computed: {
            hasForms() {
                return this.forms && this.forms.length > 0;
            },
            hasLeftControls() {
                return this.hasActiveQuery || this.count > 0 && (this.filters?.length > 0 || this.canSearch);
            },
            hasRightControls() {
                return this.canReorder || this.canCreate || !!this.primaryCommand;
            },
            hasOuterTitle() {
                return this.$slots.default && (!this.ready || this.hasLeftControls);
            },
            barVisible() {
                if(this.collapsed) {
                    return !this.hasLeftControls;
                }
                return true;
            },
            searchLabel() {
                return lang('action_bar.list.search.title').replace(':search', this.search);
            },
        },
        methods: {
            handleSearchSubmitted(search) {
                this.$emit('search-submit', search);
            },
            handleFilterChanged(filter, value) {
                this.$emit('filter-change', filter, value);
            },
            handleReorderButtonClicked() {
                this.$emit('reorder-click');
                document.activeElement.blur();
            },
            handleReorderSubmitButtonClicked() {
                this.$emit('reorder-submit');
            },
            handlePrimaryCommandClicked() {
                this.$emit('command', this.primaryCommand);
            },
            handleCreateButtonClicked() {
                this.$emit('create');
            },
            handleCreateFormSelected(form) {
                this.$emit('create', form);
            },
        },
        directives: {
            sticky,
        },
    }
</script>

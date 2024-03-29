<template>
    <div class="SharpDataList" :class="{ 'SharpDataList--reordering': reordering }" :style="styles">
        <template v-if="isEmpty">
            <div class="SharpDataList__empty">
                <slot name="prepend"></slot>
                <div class="p-3">
                    <slot name="empty" />
                    <slot name="append-body" />
                </div>
            </div>
        </template>
        <template v-else>
            <template v-if="$slots['append-head']">
                <div class="d-flex justify-content-end mb-3 d-sm-none">
                    <slot name="append-head" />
                </div>
            </template>
            <div class="SharpDataList__table SharpDataList__table--border">
                <slot name="prepend"></slot>
                <template v-if="!hideHeader">
                    <div class="SharpDataList__thead" ref="head">
                        <DataListRow :columns="columns" header>
                            <template v-slot:cell="{ column }">
                                <div class="row">
                                    <div class="col-auto">
                                        <div class="row align-items-center gx-2">
                                            <div class="col" style="min-width: 0">
                                                <div class="overflow-hidden">
                                                    {{ column.label }}
                                                </div>
                                            </div>
                                            <template v-if="column.sortable">
                                                <div class="col-auto">
                                                    <svg class="SharpDataList__caret"
                                                        :class="{
                                                            'SharpDataList__caret--selected': sort === column.key,
                                                            'SharpDataList__caret--ascending': sort === column.key && dir === 'asc'
                                                        }"
                                                        width="10" height="5" viewBox="0 0 10 5" fill-rule="evenodd"
                                                    >
                                                        <path d="M10 0L5 5 0 0z"></path>
                                                    </svg>
                                                    <a class="SharpDataList__sort-link" @click.prevent="handleSortClicked(column.key)" href=""></a>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-if="$slots['append-head']" v-slot:append>
                                <slot name="append-head" />
                            </template>
                        </DataListRow>
                    </div>
                </template>
                <div class="SharpDataList__tbody" ref="body">
                    <Draggable :options="draggableOptions" :value="reorderedItems" @input="handleItemsChanged">
                        <template v-for="item in currentItems">
                            <slot name="item" :item="item" />
                        </template>
                    </Draggable>
                    <slot name="append-body" />
                </div>
            </div>
        </template>
        <template v-if="hasPagination">
            <div class="SharpDataList__pagination-container">
                <Pagination
                    :total-rows="totalCount"
                    :per-page="pageSize"
                    :min-page-end-buttons="3"
                    :limit="7"
                    :value="page"
                    @change="handlePageChanged"
                />
            </div>
        </template>
    </div>
</template>

<script>
    import Pagination from './Pagination.vue';
    import DataListRow from './DataListRow.vue';
    import Draggable from 'vuedraggable';

    export default {
        components: {
            Pagination,
            DataListRow,
            Draggable,
        },
        props: {
            items: Array,
            columns: Array,

            paginated: Boolean,
            totalCount: Number,
            pageSize: Number,
            page: Number,

            reordering: Boolean,

            sort: String,
            dir: String,

            hideHeader: Boolean,
        },
        data() {
            return {
                reorderedItems: null,

                //layout
                prependWidth: 0,
                appendWidth: 0,
            }
        },
        watch: {
            reordering(active) {
                this.handleReorderingChanged(active);
            }
        },
        computed: {
            hasPagination() {
                return !!this.paginated && this.totalCount/this.pageSize > 1;
            },
            draggableOptions() {
                return {
                    disabled: !this.reordering
                }
            },
            currentItems() {
                return this.reordering ? this.reorderedItems : this.items;
            },
            isEmpty() {
                return (this.items||[]).length === 0;
            },
            styles() {
                return {
                    '--prepend-width': this.prependWidth ? `${this.prependWidth}px` : null,
                    '--append-width': this.appendWidth ? `${this.appendWidth}px` : null,
                }
            },
        },
        methods: {
            handleItemsChanged(items) {
                this.reorderedItems = items;
                this.$emit('change', items);
            },
            handleSortClicked(columnKey) {
                this.$emit('sort-change', {
                    prop: columnKey,
                    dir: this.sort === columnKey
                        ? (this.dir === 'asc' ? 'desc' : 'asc')
                        : 'asc'
                });
            },
            handlePageChanged(page) {
                this.$emit('page-change', page);
            },
            handleReorderingChanged(active) {
                this.reorderedItems = active ? [...this.items] : null;
            },
            async updateLayout() {
                this.appendWidth = 0;
                await this.$nextTick();
                const headAppendWidth = this.$refs.head?.querySelector('.SharpDataList__row-append')?.offsetWidth ?? 0;
                const bodyAppendWidth = this.$refs.body?.querySelector('.SharpDataList__row-append')?.offsetWidth ?? 0;
                const bodyPrependWidth = this.$refs.body?.querySelector('.SharpDataList__row-prepend')?.offsetWidth ?? 0;
                this.appendWidth = Math.max(headAppendWidth, bodyAppendWidth);
                this.prependWidth = bodyPrependWidth;
            }
        },
        updated() {
            // const headAppendWidth = this.$refs.head?.querySelector('.SharpDataList__row-append')?.offsetWidth ?? 0;
            // const bodyAppendWidth = this.$refs.body?.querySelector('.SharpDataList__row-append')?.offsetWidth ?? 0;
            // const bodyPrependWidth = this.$refs.body?.querySelector('.SharpDataList__row-prepend')?.offsetWidth ?? 0;
            // this.appendWidth = Math.max(headAppendWidth, bodyAppendWidth);
            // this.prependWidth = bodyPrependWidth;
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

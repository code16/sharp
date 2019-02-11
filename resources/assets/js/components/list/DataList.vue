<template>
    <div class="SharpDataList" :class="{ 'SharpDataList--reordering': reorderActive }">
        <template v-if="isEmpty">
            <slot name="empty" />
        </template>
        <template v-else>
            <div class="SharpDataList__table SharpDataList__table--border">
                <div class="SharpDataList__thead" ref="head">
                    <SharpDataListRow :columns="columns" header>
                        <template slot="cell" slot-scope="{ column }">
                            <span>{{ column.label }}</span>
                            <template v-if="column.sortable">
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
                            </template>
                        </template>
                        <template slot="append">
                            <div class="d-none d-md-block" :style="{ width: headerRowAppendWidth }">&nbsp;</div>
                        </template>
                    </SharpDataListRow>
                </div>
                <div class="SharpDataList__tbody" ref="body">
                    <Draggable :options="draggableOptions" :value="reorderedItems" @input="handleItemsChanged">
                        <template v-for="item in currentItems">
                            <slot name="item" :item="item">
                                <SharpDataListRow :columns="columns" :row="item" />
                            </slot>
                        </template>
                    </Draggable>
                </div>
            </div>
        </template>
        <template v-if="hasPagination">
            <div class="SharpDataList__pagination-container">
                <SharpPagination
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
    import SharpPagination from './Pagination.vue';
    import SharpDataListRow from './DataListRow.vue';
    import Draggable from 'vuedraggable';

    export default {
        components: {
            SharpPagination,
            SharpDataListRow,
            Draggable,
        },
        props: {
            items: Array,
            columns: Array,

            paginated: Boolean,
            totalCount: Number,
            pageSize: Number,
            page: Number,

            reorderActive: Boolean,

            sort: String,
            dir: String,
        },
        data() {
            return {
                reorderedItems: null,

                //layout
                headerRowAppendWidth: 0,
            }
        },
        watch: {
            reorderActive(active) {
                this.handleReorderActiveChanged(active);
            }
        },
        computed: {
            hasPagination() {
                return !!this.paginated && this.totalCount/this.pageSize > 1;
            },
            draggableOptions() {
                return {
                    disabled: !this.reorderActive
                }
            },
            currentItems() {
                return this.reorderActive
                    ? this.reorderedItems
                    : this.items;
            },
            isEmpty() {
                return (this.items||[]).length === 0;
            }
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
            handleReorderActiveChanged(active) {
                this.reorderedItems = active ? [...this.items] : null;
            },
            updateLayout() {
                const body = this.$refs.body;
                if(body) {
                    const append = body.querySelector('.SharpDataList__row-append');
                    this.headerRowAppendWidth = append ? `${append.offsetWidth}px` : 0;
                }
            }
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
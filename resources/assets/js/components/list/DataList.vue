<template>
    <div class="SharpDataList">
        <template v-if="!items.length">
            <slot name="empty" />
        </template>
        <template v-else>
            <div class="SharpDataList__table SharpDataList__table--border">
                <div class="SharpDataList__thead">
                    <div class="SharpDataList__row SharpDataList__row--header container">
                        <div class="SharpDataList__cols">
                            <div class="row">
                                <template v-for="column in columns">
                                    <div class="SharpDataList__th" :class="colClasses(column)">
                                        <span>{{ column.label }}</span>
                                        <template v-if="column.sortable">
                                            <svg class="SharpDataList__caret"
                                                :class="{
                                                  'SharpDataList__caret--selected': sortedBy === column.key,
                                                  'SharpDataList__caret--ascending': sortedBy === column.key && sortDir === 'asc'
                                                }"
                                                width="10" height="5" viewBox="0 0 10 5" fill-rule="evenodd"
                                            >
                                                <path d="M10 0L5 5 0 0z"></path>
                                            </svg>
                                            <a class="SharpDataList__sort-link" @click.prevent="handleSortClicked(column.key)" href=""></a>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="d-none d-md-block" :style="{ width: $_headerRowExtraWidth }">&nbsp;</div>
                    </div>
                </div>
                <div class="SharpDataList__tbody">
                    <draggable :options="draggableOptions" :value="reorderedItems" @input="handleItemsChanged">
                        <template v-for="item in currentItems">
                            <div class="SharpDataList__row container"
                                :class="{
                                  'SharpDataList__row--disabled': !rowHasLink(item),
                                  'SharpDataList__row--reorder': reorderActive
                                }"
                            >
                                <div class="SharpDataList__cols">
                                    <div class="row">
                                        <template v-for="column in columns">
                                            <div class="SharpDataList__td" :class="colClasses(column)">
                                                <template v-if="column.html">
                                                    <div v-html="item[column.key]" class="SharpDataList__td-html-container"></div>
                                                </template>
                                                <template v-else>
                                                    {{ item[column.key] }}
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <template v-if="rowHasLink(item)">
                                        <a class="SharpDataList__row-link" :href="rowLink(item)"></a>
                                    </template>
                                </div>
                                <template v-if="$slots['row-extra']">
                                    <div class="SharpDataList__row-extra align-self-center" ref="rowExtra">
                                        <slot name="row-extra" :item="item" />
                                    </div>
                                </template>
                            </div>
                        </template>
                    </draggable>
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
                    @change="pageChanged"
                />
            </div>
        </template>
    </div>
</template>

<script>
    import SharpPagination from './Pagination.vue';

    export default {
        components: {
            SharpPagination
        },
        props: {
            items: Array,
            columns: Array,

            paginated: Boolean,
            totalCount: Number,
            pageSize: Number,
            page: Number,

            reorderActive: Boolean,

            sortedBy: String,
            sortDir: String,
        },
        data() {
            return {
                reorderedItems: null,
                $_headerRowExtraWidth: 0,
            }
        },
        watch: {
            reorderActive(active) {
                this.reorderedItems = active ? [...this.items] : null;
            },
        },
        computed: {
            hasPagination() {
                return this.totalCount/this.pageSize > 1 && this.paginated;
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
                    dir: this.sortedBy === columnKey
                        ? (this.sortDir === 'asc' ? 'desc' : 'asc')
                        : 'asc'
                });
            },
            colClasses(column) {
                return [
                    `col-${column.sizeXS}`,
                    `col-md-${column.size}`,
                    ...(column.hideOnXS ? ['d-none d-md-flex'] : [])
                ];
            },
            updateLayout() {
                const { rowExtra } = this.$refs;
                this.$_headerRowExtraWidth = rowExtra ? `${rowExtra[0].offsetWidth}px` : 0;
            }
        },
        mounted() {
            window.addEventListener('resize', this.updateLayout);
        },
        destroyed() {
            window.removeEventListener('resize', this.updateLayout);
        }
    }
</script>
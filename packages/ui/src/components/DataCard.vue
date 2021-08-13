<template>
    <a class="card h-100 text-body text-decoration-none" :href="url" style="font-size: .875rem">
        <template v-if="hasHeader">
            <div class="card-header border-0 bg-white p-0" :class="{ 'mb-n2': bodyColumns.length }">
                <div class="row g-0">
                    <div class="col align-self-center p-3">
                        <template v-if="headerColumn">
                            <div v-html="data[headerColumn.key]"></div>
                        </template>
                    </div>

                    <template v-if="$slots.actions">
                        <div class="col-auto p-2">
                            <slot name="actions" />
                        </div>
                    </template>
                </div>
            </div>
        </template>
        <template v-if="bodyColumns.length">
            <div class="card-body">
                <div class="row gx-3 gy-4">
                    <template v-for="column in bodyColumns">
                        <template v-if="data[column.key]">
                            <div class="col-12" :class="[`col-md-${column.size}`]">
                                <template v-if="column.label">
                                    <div class="form-label" style="font-size: .875em">{{ column.label }}</div>
                                </template>
                                <div v-html="data[column.key]"></div>
                            </div>
                        </template>
                    </template>
                </div>
            </div>
        </template>
    </a>
</template>

<script>
    export default {
        props: {
            columns: Array,
            data: Object,
            url: String,
            alignColumnHeader: Boolean,
        },
        computed: {
            hasHeader() {
                return !!this.$slots.actions;
            },
            visibleColumns() {
                return (this.columns ?? []).filter(column => this.data[column.key]);
            },
            bodyColumns() {
                if(this.headerColumn) {
                    return this.visibleColumns.filter(column => this.headerColumn.key !== column.key);
                }
                return this.visibleColumns;
            },
            headerColumn() {
                return this.hasHeader && this.alignColumnHeader
                    ? this.visibleColumns[0]
                    : null;
            },
        }
    }
</script>

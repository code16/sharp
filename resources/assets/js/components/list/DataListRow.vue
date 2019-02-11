<template>
    <div class="SharpDataList__row container"
        :class="{
            'SharpDataList__row--header': header,
            'SharpDataList__row--disabled': !header && !hasLink,
        }"
    >
        <div class="SharpDataList__cols">
            <div class="row">
                <template v-for="column in columns">
                    <div :class="[
                        header ? 'SharpDataList__th' : 'SharpDataList__td',
                        colClasses(column)
                    ]">
                        <slot name="cell" :row="row" :column="column">
                            <template v-if="column.html">
                                <div v-html="row[column.key]" class="SharpDataList__td-html-container"></div>
                            </template>
                            <template v-else>
                                {{ row[column.key] }}
                            </template>
                        </slot>
                    </div>
                </template>
            </div>
            <template v-if="hasLink">
                <a class="SharpDataList__row-link" :href="url"></a>
            </template>
        </div>
        <template v-if="$slots.append">
            <div class="SharpDataList__row-append align-self-center">
                <slot name="append" />
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        props: {
            columns: Array,
            row: {
                type: Object,
                default: ()=>({})
            },
            url: String,
            header: Boolean
        },
        computed: {
            hasLink() {
                return !!this.url;
            }
        },
        methods: {
            colClasses(column) {
                return [
                    `col-${column.sizeXS}`,
                    `col-md-${column.size}`,
                    ...(column.hideOnXS ? ['d-none d-md-flex'] : [])
                ];
            },
        }
    }
</script>
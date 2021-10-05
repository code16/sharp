<template>
    <div class="SharpDataList__row container" :class="classes">
        <div class="SharpDataList__cols">
            <div class="row mx-n2 mx-md-n3">
                <template v-for="column in columns">
                    <div class="px-2 px-md-3" :class="[
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
        <template v-if="$scopedSlots.append">
            <div class="SharpDataList__row-append align-self-center">
                <slot name="append" v-bind="this" />
            </div>
        </template>
        <template v-else>
            <div class="SharpDataList__row-spacer"></div>
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
            header: Boolean,
            highlight: Boolean,
        },
        data() {
            return {
                isHighlighted: this.highlight,
            }
        },
        watch: {
            highlight() {
                this.isHighlighted = this.highlight;
            }
        },
        computed: {
            hasLink() {
                return !!this.url;
            },
            classes() {
                return {
                    'SharpDataList__row--header': this.header,
                    'SharpDataList__row--disabled': !this.header && !this.hasLink,
                    'SharpDataList__row--highlight': this.isHighlighted,
                    'SharpDataList__row--full-width': this.isFullWidth,
                }
            },
            isFullWidth() {
                return !this.$scopedSlots.append;
            },
        },
        methods: {
            colClasses(column) {
                const { size, sizeXS, hideOnXS } = column;
                return {
                    'col': sizeXS === 'fill' && !hideOnXS,
                    [`col-${sizeXS}`]: sizeXS !== 'fill' && !hideOnXS,
                    'col-md': size === 'fill',
                    [`col-md-${size}`]: size !== 'fill',
                    'd-none d-md-block': hideOnXS,
                };
            },
            toggleHighlight(highlight) {
                this.isHighlighted = highlight;
            }
        }
    }
</script>

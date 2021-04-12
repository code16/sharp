<template>
    <div class="SharpGrid">
        <template v-for="row in rows">
            <div class="SharpGrid__row row" :class="rowClasses(row)">
                <template v-for="col in row">
                    <div class="SharpGrid__col" :class="colClasses(col)" v-empty-class="'SharpGrid__col--empty'">
                        <slot :item-layout="col" />
                    </div>
                </template>
            </div>
        </template>
    </div>
</template>

<script>
    import { emptyClass } from 'sharp/directives';

    export default {
        name: 'SharpGrid',

        props: {
            rows: { // 2D array [row][col]
                type: Array,
                required: true
            },
            rowClass: [Function, String],
            colClass: {
                type: Function,
                default: () => null,
            },
        },
        methods: {
            colClasses(col) {
                const { size, sizeXS } = col;
                const hasSize = !!size;
                return [
                    {
                        [`col-${sizeXS}`]: sizeXS,
                        [`col-md-${size}`]: hasSize,
                        'col-md': !hasSize
                    },
                    this.colClass(col),
                ];
            },
            rowClasses(row) {
                if(typeof this.rowClass === 'function') {
                    return this.rowClass(row);
                }
                return this.rowClass;
            }
        },
        directives: {
            'empty-class': emptyClass
        }
    }
</script>

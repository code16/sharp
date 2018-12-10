<template>
    <div class="SharpGrid">
        <div v-for="row in rows" class="SharpGrid__row row">
            <div v-for="col in row" :class="colClasses(col)" class="SharpGrid__col" v-empty-class="'SharpGrid__col--empty'">
                <slot v-bind="col" />
            </div>
        </div>
    </div>
</template>

<script>
    import emptyClass from '../directives/EmptyClass';

    export default {
        name: 'SharpGrid',

        props: {
            rows: { // 2D array [row][col]
                type: Array,
                required: true
            }
        },
        methods: {
            colClasses({ size, sizeXS }) {
                const hasSize = !!size;
                return {
                    [`col-${sizeXS}`]: sizeXS,
                    [`col-md-${size}`]: hasSize,
                    'col-md': !hasSize
                }
            }
        },
        directives: {
            'empty-class': emptyClass
        }
    }
</script>

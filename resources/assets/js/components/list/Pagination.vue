<template>
    <b-pagination
        class="SharpPagination"
        :total-rows="totalRows"
        :per-page="perPage"
        :hide-goto-end-buttons="hideGotoEndButtons"
        v-bind="$attrs"
        v-on="$listeners"
    >
        <template slot="first-text">
            <i class="fa fa-angle-double-left" aria-hidden="true"></i>
        </template>
        <template slot="prev-text">
            <i class="fa fa-angle-left" aria-hidden="true"></i>
        </template>
        <template slot="next-text">
            <i class="fa fa-angle-right" aria-hidden="true"></i>
        </template>
        <template slot="last-text">
            <i class="fa fa-angle-double-right" aria-hidden="true"></i>
        </template>
    </b-pagination>
</template>

<script>
    import { BPagination } from 'bootstrap-vue';

    // for props/events check
    // https://bootstrap-vue.js.org/docs/components/pagination

    export default {
        name: 'SharpPagination',
        inheritAttrs: false,

        components: {
            BPagination
        },

        props: {
            totalRows: Number,
            perPage: Number,
            minPageEndButtons: {
                type: Number,
                default: 0,
            },
        },

        computed: {
            hideGotoEndButtons() {
                const numberOfPages = Math.ceil(this.totalRows / Math.max(this.perPage, 1));
                return numberOfPages < this.minPageEndButtons;
            },
        },
    }
</script>
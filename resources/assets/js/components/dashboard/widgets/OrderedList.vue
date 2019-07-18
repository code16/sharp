<template>
    <div class="SharpWidgetOrderedList w-100">
        <h2 class="mb-3">
            {{ title }}
        </h2>
        <SharpDataList class="SharpWidgetOrderedList__list" :items="items" :columns="columns" hide-header>
            <template slot="item" slot-scope="{ item }">
                <SharpDataListRow :url="item.url" :columns="columns" :row="item">
                    <template v-if="hasCount(item)">
                        <template slot="append">
                            <span class="SharpTag SharpTag--default">{{ item.count }}</span>
                        </template>
                    </template>
                </SharpDataListRow>
            </template>
        </SharpDataList>
    </div>
</template>

<script>
    import SharpDataList from '../../list/DataList';
    import SharpDataListRow from '../../list/DataListRow';

    export default {
        name: 'SharpWidgetOrderedList',

        components: {
            SharpDataList,
            SharpDataListRow,
        },

        props: {
            value: Object,
            withCounts: {
                type: Boolean,
                default: true,
            },
            title: String,
        },

        computed: {
            items() {
                return this.value.data;
            },
            columns() {
                return [
                    {
                        key: 'label',
                        size: 12,
                        sizeXS: 12,
                    }
                ]
            },
        },
        methods: {
            hasCount(item) {
                return this.withCounts && typeof item.count === 'number';
            }
        },
    }
</script>
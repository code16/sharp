<template>
    <div class="SharpWidgetOrderedList w-100">
        <h2 class="mb-3">
            {{ title }}
        </h2>
        <DataList class="SharpWidgetOrderedList__list" :items="items" :columns="columns" hide-header>
            <template slot="item" slot-scope="{ item }">
                <DataListRow :url="item.url" :columns="columns" :row="item">
                    <template v-if="hasCount(item)">
                        <template slot="append">
                            <span class="SharpTag SharpTag--default">{{ item.count }}</span>
                        </template>
                    </template>
                </DataListRow>
            </template>
        </DataList>
    </div>
</template>

<script>
    import { DataList, DataListRow } from 'sharp-ui';

    export default {
        name: 'SharpWidgetOrderedList',

        components: {
            DataList,
            DataListRow,
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
<template>
    <div class="SharpWidgetOrderedList w-100">
        <h2 class="SharpWidget__title mb-3 px-3">
            {{ title }}
        </h2>
        <DataList class="SharpWidgetOrderedList__list" :items="items" :columns="columns" hide-header>
            <template v-slot:item="{ item }">
                <DataListRow :url="item.url" :columns="columns" :row="item">
                    <template v-if="hasCount(item)" v-slot:append>
                        <span class="SharpTag SharpTag--default">{{ item.count }}</span>
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
            title: String,
            html: Boolean,
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
                        html: this.html,
                    }
                ]
            },
        },
        methods: {
            hasCount(item) {
                return typeof item.count === 'number' || !!item.count;
            }
        },
    }
</script>

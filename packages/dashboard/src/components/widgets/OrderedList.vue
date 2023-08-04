<script setup lang="ts">
    import { DataList, DataListRow } from '@sharp/ui';
    import { OrderedListWidgetData } from "@/types";

    type OrderedListItem = OrderedListWidgetData['value']['data'][0];

    defineProps<OrderedListWidgetData>();
</script>

<template>
    <div class="SharpWidgetOrderedList w-100">
        <h2 class="SharpWidget__title mb-3 px-3">
            {{ title }}
        </h2>
        <DataList class="SharpWidgetOrderedList__list"
            :items="value.data"
            :columns="[{ key: 'label', size: 12, sizeXS: 12, html: html }]"
            hide-header
        >
            <template v-slot:item="{ item }: { item:OrderedListItem }">
                <DataListRow :url="item.url" :columns="[{ key: 'label', size: 12, sizeXS: 12, html: html }]" :row="item">
                    <template v-if="item.count != null" v-slot:append>
                        <span class="SharpTag SharpTag--default">{{ item.count }}</span>
                    </template>
                </DataListRow>
            </template>
        </DataList>
    </div>
</template>

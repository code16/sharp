<template>
    <div class="SharpDataList__row container"
        :class="{ 'SharpDataList__row--disabled': !rowHasLink(item)}"
    >
        <div class="SharpDataList__cols">
            <div class="row">
                <template v-for="column in columns">
                    <div class="SharpDataList__td" :class="colClasses(column)">
                        <template v-if="column.html">
                            <div v-html="item[column.key]" class="SharpDataList__td-html-container"></div>
                        </template>
                        <template v-else>
                            {{ item[column.key] }}
                        </template>
                    </div>
                </template>
            </div>
            <template v-if="rowHasLink(item)">
                <a class="SharpDataList__row-link" :href="rowLink(item)"></a>
            </template>
        </div>
        <template v-if="$slots['row-extra']">
            <div class="SharpDataList__row-extra align-self-center" ref="rowExtra">
                <slot name="row-extra" :item="item" />
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        props: {
            url
        },
    }
</script>
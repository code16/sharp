<template>
    <div class="SharpWidgetList">
        <div v-for="(val,key) in value.data">
            <div v-if="withCounts">
                {{ key }} <span class="count">{{ val }}</span>
            </div>
            <div v-else>
                {{ val }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name:'SharpWidgetList',
        props: {
            value: Object,
            withCounts: Boolean
        }
    }
</script>
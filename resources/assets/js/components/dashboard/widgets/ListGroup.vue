<template>
    <div class="SharpWidgetListGroup">
        <h2 class="title">
            {{ title }}
        </h2>
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
        name:'SharpWidgetListGroup',
        props: {
            value: Object,
            withCounts: Boolean,
            title: String,
        }
    }
</script>
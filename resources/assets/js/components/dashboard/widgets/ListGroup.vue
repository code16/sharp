<template>
    <div class="SharpWidgetListGroup">
        <h2 class="title">
            {{ title }}
        </h2>
        <div v-for="(val,key) in value.data">
            {{ key }} | count: {{ val.count ? val.count : "n/a" }} | link: {{ val.link ? val.link : "n/a" }}
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
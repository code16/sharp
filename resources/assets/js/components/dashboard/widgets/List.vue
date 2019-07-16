<template>
    <div class="SharpWidgetList">
        <div v-for="(val,key) in value.data">
            {{ key }} | {{ val }}
        </div>
    </div>
</template>

<script>
    export default {
        name:'SharpWidgetList',
        props: {
            value: Object,
        }
    }
</script>
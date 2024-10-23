<template>
    <div class="unknown-field">
        <span>Unknown field «{{ name }}»</span>
    </div>
</template>

<script>
    export default {
        props: {
            name: String,
        },
    }
</script>

<style scoped>
    .unknown-field {
        font-family:"Courier New", Courier, monospace;
        background: rgba(255, 0, 0, .25);
        border: 1px solid #f00;
        padding: .5em 1em;
        font-weight: bold;
    }
</style>
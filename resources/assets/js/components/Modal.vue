<template>
    <div class="SharpModal" v-show="show">
        <div class="SharpModal__overlay"></div>
        <div class="SharpModal__modal"></div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                opened: false
            }
        }
    }
</script>
<template>
    <div class="ShowPictureField text-center">
        <img class="ShowPictureField__thumbnail" :src="value" alt="">
    </div>
</template>

<script>
    export default {
        props: {
            value: String,
        }
    }
</script>
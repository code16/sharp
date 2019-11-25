<template>
    <div class="ShowPictureField text-center">
        <img class="img-responsive" :src="value" alt="">
    </div>
</template>

<script>
    export default {
        props: {
            value: String,
        }
    }
</script>
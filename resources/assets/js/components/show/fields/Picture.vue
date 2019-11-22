<template>
    <div class="ShowPictureField">
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
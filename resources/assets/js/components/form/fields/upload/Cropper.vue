<template>

</template>

<script>
    import VueCropper from 'vue-cropperjs';
    export default {
        name:'SharpVueCropper',
        mixins: [VueCropper],
        mounted() {
            this.cropper.on('cropend', _=>{
               console.log('cropend');
            });
        }
    }
</script>
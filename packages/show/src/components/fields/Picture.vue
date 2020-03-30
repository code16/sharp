<template>
    <div class="ShowPictureField text-center">
        <img class="ShowPictureField__thumbnail" :src="value" alt="">
    </div>
</template>

<script>
    import { syncVisibility } from "../../util/fields/visiblity";

    export default {
        props: {
            value: String,
        },
        computed: {
            isVisible() {
                return !!this.value;
            }
        },
        created() {
            syncVisibility(this, () => this.isVisible);
        }
    }
</script>
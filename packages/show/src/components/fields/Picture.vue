<template>
    <FieldLayout class="ShowPictureField">
        <div class="text-center">
            <img class="ShowPictureField__thumbnail" :src="value" alt="">
        </div>
    </FieldLayout>
</template>

<script>
    import { syncVisibility } from "../../util/fields/visiblity";
    import FieldLayout from "../FieldLayout";

    export default {
        components: {
            FieldLayout,
        },
        props: {
            value: String,
            label: String,
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
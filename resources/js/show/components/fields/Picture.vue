<script setup lang="ts">
    import FieldLayout from "../FieldLayout.vue";
    import { ShowPictureFieldData } from "@/types";
    import { ShowFieldProps } from "../../types";

    defineProps<ShowFieldProps<ShowPictureFieldData>>();
</script>

<template>
    <FieldLayout class="ShowPictureField">
        <div class="text-center">
            <img class="ShowPictureField__thumbnail" :src="value" alt="">
        </div>
    </FieldLayout>
</template>

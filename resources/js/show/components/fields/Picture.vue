<script setup lang="ts">
    import FieldLayout from "../FieldLayout.vue";
    import { ShowPictureFieldData } from "@/types";
    import { FieldProps } from "../types";

    defineProps<FieldProps & {
        field: ShowPictureFieldData,
        value: ShowPictureFieldData['value']
    }>();
</script>

<template>
    <FieldLayout class="ShowPictureField">
        <div class="text-center">
            <img class="ShowPictureField__thumbnail" :src="value" alt="">
        </div>
    </FieldLayout>
</template>

<script setup lang="ts">
    import FieldLayout from "../FieldLayout.vue";
    import { ShowPictureFieldData } from "@/types";

    defineProps<{
        value: ShowPictureFieldData['value']
        field: ShowPictureFieldData,
    }>();
</script>

<template>
    <FieldLayout class="ShowPictureField">
        <div class="text-center">
            <img class="ShowPictureField__thumbnail" :src="value" alt="">
        </div>
    </FieldLayout>
</template>

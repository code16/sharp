<script setup lang="ts">
    import { ref } from "vue";

    const collapsed = ref(false);
</script>

<template>
    <section>
        <slot :collapsed="collapsed" :on-toggle="$event => collapsed = !$event" />
    </section>
</template>

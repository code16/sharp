<template>
    <ActionView>
        <slot />
    </ActionView>
</template>

<script>
import ActionView from "../components/ActionView.vue";

export default {
    components: { ActionView }
}
</script>

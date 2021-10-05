<template>
    <div>
        <template v-if="options.alertLevel">
            <div class="alert mb-0" :class="alertClasses">
                <slot />
            </div>
        </template>
        <template v-else>
            <slot />
        </template>
    </div>
</template>

<script>
    export default {
        props: {
            options: Object,
        },
        computed: {
            alertClasses() {
                return [
                    `alert-${this.options.alertLevel}`,
                ];
            },
        },
    }
</script>

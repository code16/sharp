<template>
    <div>
        <h2 class="SharpWidget__title mb-3">
            {{ title }}
        </h2>
        <div class="d-flex align-items-center">
            <p class="display-4 fw-bold mb-0" :style="{ color: value.data.color }">
                {{ value.data.figure }}
                <template v-if="value.data.unit">
                    <span class="fs-4 fw-normal">{{ value.data.unit }}</span>
                </template>
            </p>
            <template v-if="value.data.evolution">
                <div class="ms-3" :class="value.data.evolution.increase ? 'text-success' : 'text-danger'">
                    <template v-if="value.data.evolution.increase">
                        &uarr;
                    </template>
                    <template v-else>
                        &darr;
                    </template>
                    {{ value.data.evolution.value }}
                </div>
            </template>
        </div>
        <template v-if="link">
            <div class="text-start mt-2">
                See details
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        props: {
            title: String,
            link: String,
            value: Object,
        }
    }
</script>

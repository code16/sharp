<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { FigureWidgetData } from "@/types";

    defineProps<FigureWidgetData>();
</script>

<template>
    <div>
        <template v-if="title">
            <h2 class="SharpWidget__title mb-2">
                {{ title }}
            </h2>
        </template>
        <div class="d-flex align-items-center">
            <p class="display-5 fw-bold mb-0">
                {{ value.data.figure }}
                <template v-if="value.data.unit">
                    <span class="fs-5 fw-normal">{{ value.data.unit }}</span>
                </template>
            </p>
            <template v-if="value.data.evolution">
                <div class="d-flex align-items-center ms-3"
                    :style="{ color: value.data.evolution.increase ? '#198754' : '#dc3545' }"
                >
                    <template v-if="value.data.evolution.increase">
                        <!-- heroicons: 20/solid/arrow-up -->
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                        </svg>
                    </template>
                    <template v-else>
                        <!-- heroicons: 20/solid/arrow-down -->
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                        </svg>
                    </template>
                    <span class="ms-1">
                        {{ value.data.evolution.value }}
                    </span>
                </div>
            </template>
        </div>
        <template v-if="link">
            <div class="text-start mt-2">
                <a :href="link">
                    {{ __('sharp::dashboard.widget.link_label') }}
                </a>
            </div>
        </template>
    </div>
</template>

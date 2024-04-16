<script lang="ts" setup>
import { Toaster as Sonner, type ToasterProps } from 'vue-sonner'
import { Check, CircleAlert, TriangleAlert } from "lucide-vue-next";

const props = defineProps<ToasterProps>()
</script>

<template>
  <Sonner
    class="toaster group"
    v-bind="props"
    :toast-options="{
      classes: {
        toast: 'group toast data-[type=error]:border-destructive group-[.toaster]:items-start group-[.toaster]:bg-background group-[.toaster]:text-foreground group-[.toaster]:border-border group-[.toaster]:shadow-lg',
        title: 'group-[[data-type=error]]:text-destructive',
        description: 'group-[.toast]:text-muted-foreground',
        actionButton:
          'group-[.toast]:bg-primary group-[.toast]:text-primary-foreground',
        cancelButton:
          'group-[.toast]:bg-muted group-[.toast]:text-muted-foreground',
      },
    }"
  >
      <template #success-icon>
          <Check class="w-4 h-4 mt-1" />
      </template>
      <template #error-icon>
          <CircleAlert class="w-4 h-4 mt-1 text-destructive" />
      </template>
      <template #warning-icon>
          <TriangleAlert class="w-4 h-4 mt-1" />
      </template>
  </Sonner>
</template>

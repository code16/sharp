<script lang="ts" setup>
import { Toaster as Sonner, type ToasterProps } from 'vue-sonner'
import { Check, CircleAlert, Info, TriangleAlert } from "lucide-vue-next";

const props = defineProps<ToasterProps>()
</script>

<template>
  <Sonner
    class="toaster group"
    v-bind="props"
    :toast-options="{
      classes: {
        toast: 'group toast text-base! data-[type=info]:!border-primary/25 data-[type=error]:!border-destructive group-[.toaster]:items-start group-[.toaster]:bg-background group-[.toaster]:text-foreground group-[.toaster]:border-border group-[.toaster]:shadow-lg',
        title: 'group-[[data-type=error]]:text-destructive group-[[data-type=info]]:text-primary',
        description: 'group-[.toast]:text-muted-foreground',
        actionButton:
          'group-[.toast]:bg-primary group-[.toast]:text-primary-foreground',
        cancelButton:
          'group-[.toast]:bg-muted group-[.toast]:text-muted-foreground',
      },
    }"
  >
      <template #info-icon>
          <Info class="w-4 h-4 mt-2 text-primary" />
      </template>
      <template #success-icon>
          <Check class="w-4 h-4 mt-2" />
      </template>
      <template #error-icon>
          <CircleAlert class="w-4 h-4 mt-2 text-destructive" />
      </template>
      <template #warning-icon>
          <TriangleAlert class="w-4 h-4 mt-2 text-destructive" />
      </template>
  </Sonner>
</template>

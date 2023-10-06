<template>
    <div class="flex flex-col gap-[var(--gap-y)]"
        :style="{
            '--gap-x': `${.25 * ($attrs.class?.match(/gap-x-(\d+)/)?.[1] ?? 0)}rem`,
            '--gap-y': `${.25 * ($attrs.class?.match(/gap-y-(\d+)/)?.[1] ?? 0)}rem`,
        }"
    >
        <slot></slot>
    </div>
</template>


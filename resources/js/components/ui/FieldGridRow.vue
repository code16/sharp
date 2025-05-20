<template>
    <div class="col-span-full grid" :class="[
        'grid-cols-1 @md/field-container:grid-cols-12', // must be @md for command modal form
        // '@3xl/root-card:@md/field-container:grid-cols-12',
        'gap-y-[inherit] gap-x-2 @xs/field-container:gap-x-[inherit]',
    ]">
        <slot></slot>
    </div>
</template>

<template>
     <li class="SharpLeftNav__item"
         :class="{'SharpLeftNav__item--active':current, 'SharpLeftNav__item--disabled':disabled}"
         role="menuitem"
     >
         <template v-if="disabled">
             <span class="SharpLeftNav__item-link">
                 <slot></slot>
             </span>
         </template>
         <template v-else>
             <a class="SharpLeftNav__item-link" :href="href" target="_blank">
                 <slot></slot>
             </a>
         </template>
     </li>
</template>

<script>
    export default {
        name: 'SharpNavItem',
        props: {
            current: {
                type:Boolean,
                default: false
            },
            disabled: {
                type: Boolean,
                default: false
            },
            href: String,
        },
    }
</script>

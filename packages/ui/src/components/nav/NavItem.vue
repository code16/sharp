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
             <a class="SharpLeftNav__item-link abc" :href="link" :target="hrefTarget">
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
            link: String,
            newWindow: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {

            }
        },
        computed: {
            hrefTarget() {
                return (this.newWindow ? "_blank" : "_self")
            }
        }
    }
</script>
<template>
     <li class="SharpLeftNav__item"
         :class="{'SharpLeftNav__item--active':current, 'SharpLeftNav__item--disabled':disabled}"
         role="menuitem"
     >
         <template v-if="disabled">
             <span class="SharpLeftNav__item-link" :class="linkClass">
                 <slot></slot>
             </span>
         </template>
         <template v-else>
             <component
                 :is="target === '_blank' ? 'a' : Link"
                 class="SharpLeftNav__item-link"
                 :class="linkClass" :href="href"
                 :target="target"
             >
                 <slot></slot>
             </component>
         </template>
     </li>
</template>

<script>
    import { Link } from "@inertiajs/vue3";

    export default {
        name: 'SharpNavItem',
        components: {
            Link
        },
        props: {
            disabled: {
                type: Boolean,
                default: false
            },
            href: String,
            target: String,
            linkClass: String,
            entityKey: String,
        },
        data() {
            return {
                Link,
            }
        },
        computed: {
            current() {
                return this.$page.props.currentEntity?.key === this.entityKey;
            },
        }
    }
</script>

<template>
    <li class="SharpLeftNav__item SharpLeftNav__item--has-children"
        :class="{'SharpLeftNav__item--expanded': expanded}">
        <a class="SharpLeftNav__item-link" @click="expanded=!expanded">
            <slot name="label">
                {{ label }}
            </slot>
            <div class="SharpLeftNav__item-icon">
                <svg class="SharpLeftNav__icon" width="10" height="5" viewBox="0 0 10 5" fill-rule="evenodd">
                    <path d="M10 0L5 5 0 0z"></path>
                </svg>
            </div>
        </a>
        <ul role="menu" aria-hidden="true" class="SharpLeftNav__list SharpLeftNav__list--nested">
            <slot></slot>
        </ul>
    </li>
</template>

<script>
    export default {
        name:'SharpCollapsibleItem',
        props: {
            isExpanded: {
                type:Boolean,
                default: false
            },
            label: String
        },
        data() {
            return {
                expanded: this.isExpanded
            }
        },
        mounted() {
            console.log(this.$slots.default);
        }
    }
</script>
<template>
    <li class="SharpDropdown__item">
        <a class="SharpDropdown__link" :class="customClass" href="" @mousedown.prevent="" @click.prevent="handleClick">
            <slot></slot>
        </a>
    </li>
</template>

<script>
    export default {
        name: 'SharpDropdownItem',
        inject: ['$dropdown'],

        props: {
            customClass: [String, Object]
        },

        methods: {
            handleClick(e) {
                this.$emit('click',e);
                this.$dropdown.$el.blur();
            }
        }
    }
</script>
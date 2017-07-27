<template>
    <div class="SharpDropdown" :class="{'SharpDropdown--open':opened}" tabindex="0" @focus="opened=true" @blur="opened=false">
        <li class="SharpDropdown__text">
            <slot name="text">{{text}}</slot>
        </li>
        <dropdown-arrow v-if="showArrow" class="SharpDropdown__arrow"></dropdown-arrow>
        <li>
            <ul class="SharpDropdown__list">
                <slot></slot>
            </ul>
        </li>
    </div>
</template>

<script>
    import DropdownArrow from './Arrow';

    export default {
        name: 'SharpDropdown',
        components: {
            DropdownArrow
        },
        provide() {
            return  {
                $dropdown: this
            }
        },
        props: {
            text: String,
            showArrow: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                opened: false
            }
        },
        methods:{
            toggle() {
                this.opened = !this.opened;
            }
        },
        mounted() {
        }
    }
</script>
<template>
    <div class="SharpDropdown"
         :class="{'SharpDropdown--open':opened,
         'SharpDropdown--disabled':disabled,
         'SharpDropdown--above':isAbove,
         'SharpDropdown--no-arrow':!showArrow}"
         :tabindex="disabled?-1:0"
         @focus="handleFocus" @blur="handleBlur">
        <div class="SharpDropdown__text" @mousedown="toggleIfFocused">
            <slot name="text">{{text}}</slot>
        </div>
        <dropdown-arrow v-if="showArrow" class="SharpDropdown__arrow"></dropdown-arrow>
        <div v-if="!disabled">
            <ul class="SharpDropdown__list" ref="list">
                <slot></slot>
            </ul>
        </div>
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
            },
            disabled: Boolean
        },
        data() {
            return {
                opened: false,
                isAbove: false
            }
        },
        watch: {
            opened(val) {
                if(val) {
                    this.$nextTick(_=> this.$emit('opened'));
                }
            }
        },
        methods:{
            async handleFocus() {
                if(this.disabled) return;
                this.opened = true;
                await this.$nextTick();
                this.adjustPosition();
            },
            handleBlur() {
                this.opened = false;
                this.isAbove = false;
            },
            toggleIfFocused(e) {
                if(this.opened) {
                    this.$el.blur();
                    e.preventDefault();
                }
            },
            adjustPosition () {
                let { bottom } = this.$refs.list.getBoundingClientRect();
                this.isAbove = bottom > window.innerHeight;
            }
        },
        mounted() {
        }
    }
</script>
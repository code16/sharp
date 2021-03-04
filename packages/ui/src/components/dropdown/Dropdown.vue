<template>
    <b-dropdown :disabled="disabled" :no-caret="!showArrow">
        <template v-slot:button-content>
            <slot name="text">{{text}}</slot>
        </template>

        <slot />
    </b-dropdown>
<!--    <div class="SharpDropdown"-->
<!--         :class="{'SharpDropdown&#45;&#45;open':opened,-->
<!--         'SharpDropdown&#45;&#45;disabled':disabled,-->
<!--         'SharpDropdown&#45;&#45;above':isAbove,-->
<!--         'SharpDropdown&#45;&#45;no-arrow':!showArrow}"-->
<!--         :tabindex="disabled?-1:0"-->
<!--         @focus="handleFocus" @blur="handleBlur">-->
<!--        <div class="SharpDropdown__text" @mousedown="toggleIfFocused">-->
<!--            -->
<!--        </div>-->
<!--        <dropdown-arrow v-if="showArrow" class="SharpDropdown__arrow"></dropdown-arrow>-->
<!--        <div v-if="!disabled">-->
<!--            <ul class="SharpDropdown__list" ref="list">-->
<!--                <slot></slot>-->
<!--            </ul>-->
<!--        </div>-->
<!--    </div>-->
</template>

<script>
    import DropdownArrow from './Arrow';
    import { BDropdown } from 'bootstrap-vue';

    export default {
        name: 'SharpDropdown',
        components: {
            DropdownArrow,
            BDropdown,
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
            // async handleFocus() {
            //     if(this.disabled) return;
            //     this.opened = true;
            //     await this.$nextTick();
            //     this.adjustPosition();
            // },
            // handleBlur() {
            //     this.opened = false;
            //     this.isAbove = false;
            // },
            // toggleIfFocused(e) {
            //     if(this.opened) {
            //         this.$el.blur();
            //         e.preventDefault();
            //     }
            // },
            // adjustPosition () {
            //     let { bottom } = this.$refs.list.getBoundingClientRect();
            //     this.isAbove = bottom > window.innerHeight;
            // }
        },
    }
</script>

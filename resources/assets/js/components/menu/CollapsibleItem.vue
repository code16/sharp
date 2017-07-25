<template>
    <li class="SharpLeftNav__item SharpLeftNav__item--has-children"
        :class="{'SharpLeftNav__item--expanded': expanded}" tabindex="0" @keydown.enter="toggle">
        <a class="SharpLeftNav__item-link" @click="toggle">
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
    import NavItem from './NavItem';
    export default {
        name:'SharpCollapsibleItem',
        props: {
            initiallyExpanded: {
                type:Boolean,
                default: false
            },
            label: String
        },
        data() {
            return {
                expanded: this.initiallyExpanded
            }
        },
        watch: {
            expanded:'expandedChanged'
        },
        computed: {
            navItems() {
                return this.$slots.default
                    .map(slot => slot.componentInstance)
                    .filter(comp => comp && comp.$options.name === NavItem.name);
            }
        },
        methods: {
            expandedChanged() {
                this.$nextTick(_=> {
                    this.navItems.forEach(i => i.itemLinkFocusable = this.expanded);
                });
            },
            toggle() {
                this.expanded = !this.expanded;
            }
        },
        mounted() {
            this.navItems.forEach(i => {
                i.itemFocusable = false;
                i.itemLinkFocusable = this.expanded;
            });
        }
    }
</script>
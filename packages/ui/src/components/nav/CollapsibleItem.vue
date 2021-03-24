<template>
    <li class="SharpLeftNav__item SharpLeftNav__item--has-children"
        :class="{'SharpLeftNav__item--expanded': expanded}"
        tabindex="0"
        @keydown.enter="toggle"
    >
        <a class="SharpLeftNav__item-link" @click="toggle">
            <div class="row gx-2 align-items-center flex-nowrap">
                <div class="col" style="min-width: 0">
                    <slot name="label">
                        {{ label }}
                    </slot>
                </div>
                <div class="col-auto">
                    <div class="SharpLeftNav__item-icon">
                        <svg class="SharpLeftNav__icon" width="10" height="5" viewBox="0 0 10 5" fill-rule="evenodd">
                            <path d="M10 0L5 5 0 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </a>
        <b-collapse :visible="expanded">
            <ul role="menu" aria-hidden="true" class="SharpLeftNav__list SharpLeftNav__list--nested">
                <slot></slot>
            </ul>
        </b-collapse>
    </li>
</template>

<script>
    import NavItem from './NavItem';
    import { BCollapse } from 'bootstrap-vue';

    export default {
        components: {
            BCollapse
        },
        props: {
            label: String,
            opened: Boolean,
        },
        data() {
            return {
                expanded: this.opened,
            }
        },
        methods: {
            toggle() {
                this.expanded = !this.expanded;
            }
        },
    }
</script>

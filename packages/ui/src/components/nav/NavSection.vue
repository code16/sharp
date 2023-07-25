<template>
    <li class="SharpLeftNav__item SharpLeftNav__item--has-children"
        :class="{
            'SharpLeftNav__item--expanded': expanded,
            'SharpLeftNav__item--disabled': !collapsible,
        }"
    >
        <div class="SharpLeftNav__item-link"
            :tabindex="collapsible ? '0' : '-1'"
            @keydown.enter="toggle"
            @click="toggle"
        >
            <div class="row gx-2 align-items-center flex-nowrap">
                <div class="col" style="min-width: 0">
                    <slot name="label">
                        {{ label }}
                    </slot>
                </div>
                <template v-if="collapsible">
                    <div class="col-auto">
                        <div class="SharpLeftNav__item-icon">
                            <svg class="SharpLeftNav__icon" width="10" height="5" viewBox="0 0 10 5" fill-rule="evenodd">
                                <path d="M10 0L5 5 0 0z"></path>
                            </svg>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <b-collapse :visible="expanded">
            <ul role="menu" aria-hidden="true" class="SharpLeftNav__list SharpLeftNav__list--nested">
                <slot></slot>
            </ul>
        </b-collapse>
    </li>
</template>

<script>
    import NavItem from './NavItem.vue';
    // import { BCollapse } from 'bootstrap-vue';

    export default {
        components: {
            BCollapse: {
                template: '<div style="display: none"><slot /></div>',  // todo
            },
        },
        props: {
            label: String,
            opened: Boolean,
            collapsible: Boolean,
        },
        data() {
            return {
                expanded: this.opened || !this.collapsible,
            }
        },
        methods: {
            toggle() {
                if(this.collapsible) {
                    this.expanded = !this.expanded;
                }
            }
        },
    }
</script>

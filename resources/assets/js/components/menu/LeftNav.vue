<template>
    <nav class="SharpLeftNav"
        :class="classes"
        role="navigation"
        aria-label="Menu Sharp"
        @click="handleClicked"
    >
        <div class="SharpLeftNav__top-icon">
            <i class="fa" :class="currentIcon"></i>
        </div>
        <div class="SharpLeftNav__title-container">
            <h2 class="SharpLeftNav__title">{{ title }}</h2>
        </div>
        <template v-if="ready">
            <div class="SharpLeftNav__content d-flex flex-column h-100">
                <div class="SharpLeftNav__inner flex-grow-1" style="min-height: 0">
                    <GlobalFilters @open="handleGlobalFilterOpened" @close="handleGlobalFilterClosed" />
                    <slot />
                </div>
                <div class="flex-grow-0">
                    <div class="SharpLeftNav__collapse" @click.stop="collapsed = !collapsed">
                        <a class="SharpLeftNav__collapse-link" href="#" @click.prevent>
                            <svg class="SharpLeftNav__collapse-arrow" width="8" height="12" viewBox="0 0 8 12" fill-rule="evenodd">
                                <path d="M7.5 10.6L2.8 6l4.7-4.6L6.1 0 0 6l6.1 6z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </template>
        <template v-else>
            <div class="d-flex align-items-center justify-content-center h-100">
                <SharpLoading visible inline small />
            </div>
        </template>
    </nav>
</template>

<script>
    import { Responsive } from '../../mixins';
    import GlobalFilters from './GlobalFilters.vue';
    import { SharpLoading } from "../ui";

    export default {
        name: 'SharpLeftNav',

        mixins: [Responsive('lg')],

        components: {
            GlobalFilters,
            SharpLoading,
        },

        props: {
            items: Array,
            current: String,
            title: String,
            collapseable: {
                type: Boolean,
                default: true,
            },
            hasGlobalFilters: Boolean,
        },
        data() {
            return {
                ready: false,
                collapsed: false,
                state: 'expanded',

                filterOpened: false,
            }
        },
        watch: {
            collapsed: {
                handler(val) {
                    this.$root.$emit('setClass', 'leftNav--collapsed', this.collapsed);
                    // apply transition
                    this.state = val ? 'collapsing' : 'expanding';
                    setTimeout(this.updateState, 250);
                }
            }
        },
        computed: {
            flattenedItems() {
                return this.items.reduce((res, item) =>
                    item.type==='category'
                        ? [ ...res, ...item.entities ]
                        : [ ...res, item ]
                ,[]);
            },
            currentIcon() {
                return this.current === 'dashboard'
                    ? 'fa-dashboard'
                    : (this.flattenedItems.find(e => e.key===this.current)||{}).icon;
            },
            classes() {
                return [
                    `SharpLeftNav--${this.state}`,
                    {
                        'SharpLeftNav--filter-opened': this.filterOpened,
                        'SharpLeftNav--collapseable': this.collapseable,
                    }
                ]
            }
        },
        methods: {
            updateState() {
                this.state = this.collapsed ? 'collapsed' : 'expanded';
            },
            handleGlobalFilterOpened() {
                this.filterOpened = true;
            },
            handleGlobalFilterClosed() {
                this.filterOpened = false;
            },
            handleClicked() {
                if(this.collapsed) {
                    this.collapsed = false;
                }
            },
            async init() {
                if(this.hasGlobalFilters) {
                    await this.$store.dispatch('global-filters/get');
                }
                this.ready = true;
            },
        },
        created() {
            this.collapsed = this.isViewportSmall;
            this.updateState();
            this.init();
        },
    }
</script>
<template>
    <div>
        <nav v-show="ready" class="SharpLeftNav SharpLeftNav--collapseable" :class="`SharpLeftNav--${state}`"
             role="navigation" aria-label="Menu Sharp" @click="collapsed && (collapsed=false)">
            <div class="SharpLeftNav__top-icon">
                <i class="fa" :class="currentIcon"></i>
            </div>
            <div class="SharpLeftNav__title-container">
                <h2 class="SharpLeftNav__title">{{ title }}</h2>
            </div>
            <div class="SharpLeftNav__content d-flex flex-column h-100">
                <div class="flex-grow-1" style="min-height: 0; overflow: auto">
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
        </nav>
    </div>
</template>

<script>
    import { Responsive } from '../../mixins';

    export default {
        name: 'SharpLeftNav',

        mixins: [Responsive('lg')],

        props: {
            items: Array,
            current: String,
            title: String
        },
        data() {
            return {
                collapsed: null,
                state: 'expanded',
                ready: false
            }
        },
        watch: {
            collapsed: {
                immediate: true,
                handler(val, oldVal) {
                    if(oldVal === null) {
                        return this.updateState();
                    }
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
            }
        },
        methods: {
            updateState() {
                this.state = this.collapsed ? 'collapsed' : 'expanded';
            }
        },
        mounted() {
            this.collapsed = this.isViewportSmall;
            this.$nextTick(_=>this.ready=true);
        }
    }
</script>
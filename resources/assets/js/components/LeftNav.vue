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
        <div class="flex-grow-0">
            <div class="SharpLeftNav__title-container position-relative">
                <slot name="title">
                    <h2 class="SharpLeftNav__title mb-0">{{ title }}</h2>
                </slot>
            </div>
        </div>
        <template v-if="!hidden">
            <div class="flex-grow-1 position-relative" style="min-height: 0">
                <div class="SharpLeftNav__content d-flex flex-column">
                    <div class="SharpLeftNav__inner flex-grow-1 pb-5" style="min-height: 0">
                        <slot />
                    </div>
                    <a class="SharpLeftNav__collapse-button btn btn-text" href="#" @click.prevent.stop="collapsed = !collapsed">
                        <svg class="SharpLeftNav__collapse-arrow" style="fill:currentColor" width="8" height="12" viewBox="0 0 8 12" fill-rule="evenodd">
                            <path d="M7.5 10.6L2.8 6l4.7-4.6L6.1 0 0 6l6.1 6z"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </template>
    </nav>
</template>

<script>
    import { Loading } from 'sharp-ui';
    import { Responsive } from 'sharp/mixins';

    export default {
        name: 'SharpLeftNav',

        mixins: [Responsive('lg')],

        components: {
            Loading,
        },

        props: {
            currentEntity: Object,
            title: String,
            collapseable: {
                type: Boolean,
                default: true,
            },
            hidden: Boolean,
        },
        data() {
            return {
                ready: false,
                collapsed: false,
                state: 'expanded',
            }
        },
        watch: {
            collapsed: {
                handler(val) {
                    document.body.classList.toggle('leftNav--collapsed', this.collapsed);
                    // apply transition
                    this.state = val ? 'collapsing' : 'expanding';
                    setTimeout(this.updateState, 250);
                }
            }
        },
        computed: {
            currentIcon() {
                return this.currentEntity?.icon;
            },
            classes() {
                return [
                    `SharpLeftNav--${this.state}`,
                    {
                        'SharpLeftNav--collapseable': this.collapseable,
                        'SharpLeftNav--hidden': this.hidden,
                    }
                ]
            }
        },
        methods: {
            updateState() {
                this.state = this.collapsed ? 'collapsed' : 'expanded';
            },
            handleClicked() {
                if(this.collapsed) {
                    this.collapsed = false;
                }
            },
            async init() {
                this.$store.dispatch('setCurrentEntity', this.currentEntity);
                this.ready = true;
            },
        },
        created() {
            this.collapsed = this.isViewportSmall && !this.hidden;
            this.updateState();
            this.init();

            if(this.hidden) {
                document.body.classList.add('leftNav--hidden');
            }
        },
    }
</script>

<template>
    <div :id="id || null" class="SharpTabs" :class="classes">
        <div class="SharpTabs__inner mb-3" :class="{ 'm-sm-0':!hasNavOverflow  }">
            <template v-if="hasNavOverflow">
                <slot name="nav-prepend" />
            </template>
            <button class="SharpTabs__collapse-btn btn btn-outline-primary mb-1"
                :class="{ 'd-none':!hasNavOverflow }"
                @click="handleCollapseClicked"
            >
                <template v-if="tabs[currentTab]">
                    <span :class="dropdownButtonClasses">{{ tabs[currentTab].title }}</span>
                </template>
                <DropdownArrow class="ml-1" :style="expanded && 'transform: rotate(180deg)'" />
            </button>
            <b-collapse id="tabs" :visible="expanded" :class="{ 'd-block':!hasNavOverflow }">
                <div class="SharpTabs__nav mb-0 mb-sm-3"
                    role="tablist"
                    :aria-setsize="tabs.length"
                    :aria-posinset="currentTab + 1"
                    ref="nav"
                >
                    <template v-if="!hasNavOverflow">
                        <slot name="nav-prepend" />
                    </template>

                    <template v-for="tab in tabs">
                        <a class="SharpTabs__nav-link"
                            :class="linkClasses(tab)"
                            :href="tab.href"
                            role="tab"
                            :aria-selected="tab.localActive ? 'true' : 'false'"
                            :aria-controls="tab.id || null"
                            :id="tab.controlledBy || null"
                            @click.prevent.stop="clickTab(tab)"
                            @keydown.space.prevent.stop="clickTab(tab)"
                            @keydown.left="previousTab"
                            @keydown.up="previousTab"
                            @keydown.right="nextTab"
                            @keydown.down="nextTab"
                            v-html="tab.title"
                        ></a>
                    </template>
                </div>
            </b-collapse>
        </div>


        <div class="tab-content" ref="tabsContainer">
            <slot></slot>
            <slot name="empty" v-if="!tabs || !tabs.length"></slot>
        </div>
    </div>
</template>

<script>
    import debounce from 'lodash/debounce';
    import { BTabs, BCollapse } from 'bootstrap-vue';
    import DropdownArrow from './dropdown/Arrow';
    import { Responsive } from "sharp/mixins";

    export default {
        name: 'SharpBTabs',

        mixins: [Responsive('sm')],

        extends: BTabs,
        components: {
            BCollapse,
            DropdownArrow
        },

        data() {
            return {
                expanded: true,
                hasNavOverflow: false,
                extraNavGhostWidth: 0
            }
        },
        watch: {
            currentTab() {
                if(this.hasNavOverflow) {
                    this.expanded = false;
                }
            }
        },
        computed: {
            collapseActivated() {
                return this.isViewportSmall && (this.hasNavOverflow || this.tabs.length>2);
            },
            tabsHaveError() {
                return this.tabs.some(tab => tab.hasError);
            },
            dropdownButtonClasses() {
                return this.tabs[this.currentTab].hasError ? 'error-dot' : this.tabsHaveError ? 'error-dot--partial' : '';
            },
            classes() {
                return {
                    'SharpTabs--collapse': this.collapseActivated,
                    'SharpTabs--nav-overflow': this.hasNavOverflow,
                }
            },
        },
        methods : {
            linkClasses(tab) {
                return {
                    'SharpTabs__nav-link--has-error': tab.hasError,
                    'SharpTabs__nav-link--active': tab.localActive,
                    'SharpTabs__nav-link--disabled': tab.disabled
                };
            },
            handleCollapseClicked() {
                this.expanded = !this.expanded;
            },
            async layout() {
                this.hasNavOverflow = false;
                await this.$nextTick();
                const nav = this.$refs.nav;
                this.hasNavOverflow = nav.scrollWidth > nav.offsetWidth;
            },
        },
        mounted() {
            this.layout();
            this.debouncedLayout = debounce(this.layout, 150);
            window.addEventListener('resize', this.debouncedLayout);
        },
        destroyed() {
            window.removeEventListener('resize', this.debouncedLayout);
        },
    }
</script>


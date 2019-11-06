<template>
    <div :id="id || null" class="SharpTabs" :class="{ 'SharpTabs--collapse':collapseActivated , 'SharpTabs--nav-overflow':hasNavOverflow }">
        <div class="mb-3" :class="{ 'm-sm-0':!hasNavOverflow  }">
            <slot v-if="hasNavOverflow" name="nav-prepend"></slot>
            <button class="SharpTabs__collapse-btn SharpButton SharpButton--secondary mb-1"
                    :class="{ 'd-none':!hasNavOverflow }"
                    @click="expanded=!expanded"
            >
                <span v-if="tabs[currentTab]" :class="dropdownButtonClasses">{{ tabs[currentTab].title }}</span>
                <dropdown-arrow class="ml-1" :style="expanded && 'transform: rotate(180deg)'"/>
            </button>
            <div class="SharpTabs__nav SharpTabs__nav--ghost m-0 p-0" style="height:0;overflow: hidden" v-has-overflow.width="hasNavOverflow">
                <div :style="{minWidth:`${extraNavGhostWidth}px`}">&nbsp;</div>
                <a v-for="tab in tabs" class="SharpTabs__nav-link" v-html="tab.title"></a>
            </div>
            <b-collapse id="tabs" :visible="expanded" :class="{ 'd-block':!hasNavOverflow }">
                <div class="SharpTabs__nav mb-0 mb-sm-3"
                     role="tablist"
                     :aria-setsize="tabs.length"
                     :aria-posinset="currentTab + 1"
                >
                    <slot v-if="!hasNavOverflow" name="nav-prepend"></slot>
                    <a v-for="tab in tabs"
                        class="SharpTabs__nav-link"
                        :class="{'SharpTabs__nav-link--has-error':tab.hasError,
                         'SharpTabs__nav-link--active': tab.localActive,
                         'SharpTabs__nav-link--disabled': tab.disabled}"
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
    import { BTabs, BCollapse } from 'bootstrap-vue';
    import DropdownArrow from './dropdown/Arrow.vue';
    import HasOverflow from '../directives/HasOverflow';
    import { Responsive } from '../mixins';

    export default {
        name:'SharpBTabs',

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
            }
        },
        methods : {
            addExtraNavGhostWidthForSlot(name) {
                let $slot = this.$slots[name];
                if($slot && $slot[0] && $slot[0].elm) {
                    this.extraNavGhostWidth += $slot[0].elm.offsetWidth;
                }
            }
        },
        mounted() {
            this.addExtraNavGhostWidthForSlot('nav-prepend');
        },
        directives: {
            HasOverflow
        }
    }
</script>


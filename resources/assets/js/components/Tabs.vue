<template>
    <div :id="id || null" class="SharpTabs" :class="{ 'SharpTabs--collapse':collapseActivated , 'SharpTabs--nav-overflow':hasNavOverflow }">
        <div class="mb-3" :class="{ 'm-sm-0':!hasNavOverflow  }">
            <slot v-if="hasNavOverflow" name="nav-prepend"></slot>
            <button class="SharpTabs__collapse-btn SharpButton SharpButton--secondary mb-1"
                    :class="{ 'd-none':!hasNavOverflow }"
                    @click="expanded=!expanded"
            >
                {{ tabs[currentTab] && tabs[currentTab].title }} <sharp-dropdown-arrow class="ml-1" :style="expanded && 'transform: rotate(180deg)'"/>
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
                     @keydown.left="previousTab"
                     @keydown.up="previousTab"
                     @keydown.right="nextTab"
                     @keydown.down="nextTab"
                     @keydown.shift.left="setTab(-1,false,1)"
                     @keydown.shift.up="setTab(-1,false,1)"
                     @keydown.shift.right="setTab(tabs.length,false,-1)"
                     @keydown.shift.down="setTab(tabs.length,false,-1)"
                >
                    <slot v-if="!hasNavOverflow" name="nav-prepend"></slot>
                    <a v-for="(tab, index) in tabs"
                       class="SharpTabs__nav-link"
                       :class="{'SharpTabs__nav-link--has-error':tab.hasError,
                        'SharpTabs__nav-link--active': tab.localActive,
                        'SharpTabs__nav-link--disabled': tab.disabled}"
                       :href="tab.href"
                       role="tab"
                       :aria-selected="tab.localActive ? 'true' : 'false'"
                       :aria-controls="tab.id || null"
                       :id="tab.controlledBy || null"
                       @click.prevent.stop="setTab(index)"
                       @keydown.space.prevent.stop="setTab(index)"
                       @keydown.enter.prevent.stop="setTab(index)"
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
    import Vue from 'vue';
    import Tabs from 'bootstrap-vue/es/components/tabs/tabs'
    import bCollapse from 'bootstrap-vue/es/components/collapse/collapse';
    import SharpDropdownArrow from './dropdown/Arrow.vue';
    import HasOverflow from '../directives/HasOverflow';
    import { Responsive } from '../mixins';

    export default {
        name:'SharpBTabs',

        mixins: [Responsive('sm')],

        extends: Tabs,
        components: {
            bCollapse,
            SharpDropdownArrow
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


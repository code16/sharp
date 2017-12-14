<template>
    <div :id="id || null" class="SharpTabs">
        <div class="SharpTabs__nav"
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

        <div class="tab-content" ref="tabsContainer">
            <slot></slot>
            <slot name="empty" v-if="!tabs || !tabs.length"></slot>
        </div>
    </div>
</template>

<script>
    import Vue from 'vue';
    import Tabs from 'bootstrap-vue/es/components/tabs/tabs'

    export default {
        name:'SharpBTabs',
        extends: Tabs,
        mounted() {
            //console.log(this);
        }
    }
</script>


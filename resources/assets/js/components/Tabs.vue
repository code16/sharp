<template>
    <div :id="id || null" class="SharpTabs tabs">
        <div>
            <ul :class="['nav','nav-' + navStyle]"
                role="tablist"
                tabindex="0"
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
                <li class="nav-item" v-for="(tab, index) in tabs" role="presentation" :class="{'has-error':tab.hasError}">
                    <a :class="['nav-link',{small: small, active: tab.localActive, disabled: tab.disabled}]"
                       :href="tab.href"
                       role="tab"
                       :aria-selected="tab.localActive ? 'true' : 'false'"
                       :aria-controls="tab.id || null"
                       :id="tab.controlledBy || null"
                       @click.prevent.stop="setTab(index)"
                       @keydown.space.prevent.stop="setTab(index)"
                       @keydown.enter.prevent.stop="setTab(index)"
                       tabindex="-1"
                       v-html="tab.title"
                    ></a>
                </li>
                <slot name="tabs"></slot>
            </ul>
        </div>

        <div class="tab-content" ref="tabsContainer">
            <slot></slot>
            <slot name="empty" v-if="!tabs || !tabs.length"></slot>
        </div>
    </div>
</template>

<script>
    import bTabs from './vendor/bootstrap-vue/components/tabs';

    export default {
        name:'SharpBTabs',
        extends:bTabs
    }
</script>


<template>
    <div class="SharpActionView container">
        <sharp-action-bar>
            <template slot="left">
                <slot name="action-bar-left"></slot>
            </template>
            <template slot="right">
                <slot name="action-bar-right"></slot>
            </template>
        </sharp-action-bar>
        <slot></slot>
    </div>
</template>

<script>
    import ActionBar from './ActionBar';

    export default {
        name:'SharpActionView',
        components:{
            [ActionBar.name]: ActionBar
        }
    }
</script>
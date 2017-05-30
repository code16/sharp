<template>
    <div class="SharpActionView container">
        <div class="SharpActionView__action-bar">
            <button class="btn btn-primary" @click="$emit('main-button-click')">{{mainButtonText}}</button>
        </div>
        <slot></slot>
    </div>
</template>

<script>
    export default {
        name:'SharpActionView',
        props: {
            mainButtonText:String,
            mainButtonAction:Function
        }
    }
</script>
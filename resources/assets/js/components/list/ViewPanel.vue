<template>
    <transition enter-class="SharpViewPanel--collapsed"
                enter-active-class="SharpViewPanel--expanding"
                leave-active-class="SharpViewPanel--collapsing">
        <div v-html="content" class="SharpViewPanel" v-show="show"></div>
    </transition>
</template>

<script>
    export default {
        model: {
            prop:'show',
            event:'change'
        },
        props: {

        }
    }
</script>
<template>
    <transition :name="name" appear>
        <slot />
    </transition>
</template>

<script>
    export default {
        props: {
            fade: Boolean,
        },
        computed: {
            name() {
                if(this.fade) {
                    return 'loading-fade';
                }
            }
        },
    }
</script>

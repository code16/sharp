<template>
    <div class="SharpLoading__overlay" v-show="visible">
        <Loading visible v-bind="$attrs" />
    </div>
</template>

<script>
    import Loading from './Loading';

    export default {
        components: {
            Loading,
        },
        props: {
            visible: Boolean,
        },
    }
</script>
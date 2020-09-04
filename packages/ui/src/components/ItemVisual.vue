<template>
    <span>
        <template v-if="item.icon">
            <i class="fa" :class="iconClasses"></i>
        </template>
        <template v-else-if="item.image">
            <img :src="item.image">
        </template>
    </span>
</template>

<script>
    export default {
        name:'SharpItemVisual',
        props: {
            item: Object,
            iconClass: String,
            imageClass: String
        },
        computed: {
            iconClasses() {
                let classes = [this.item.icon];
                this.iconClass && classes.push(this.iconClass);
                return classes;
            }
        }
    }
</script>
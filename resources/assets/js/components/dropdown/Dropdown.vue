<template>
    <div>
        <div v-show="false">
            <slot></slot>
        </div>
        <sharp-multiselect :options="options"></sharp-multiselect>
    </div>
</template>

<script>
    import Multiselect from '../Multiselect';

    export default {
        name: 'SharpDropdown',
        components: {
            [Multiselect.name]: Multiselect
        },
        computed: {
            options() {
                let slots = this.$slots.default;
                //console.log(slots);
                if(slots)
                    return slots.map(vnode=>vnode.componentInstance.$el.innerText);
                return [];
            }
        },
        mounted() {
            console.log(this.$slots.default);
        }
    }
</script>
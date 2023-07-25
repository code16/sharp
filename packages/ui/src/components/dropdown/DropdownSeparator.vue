<template>
    <b-dropdown-divider />
</template>

<script>
    // import { BDropdownDivider } from 'bootstrap-vue';

    export default {
        name: 'SharpDropdownSeparator',
        components: {
            BDropdownDivider:{
                template: '<div><slot /></div>', // todo
            },
        }
    }
</script>

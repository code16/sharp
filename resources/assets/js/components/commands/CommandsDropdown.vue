<template>
    <SharpDropdown class="SharpCommandsDropdown">
        <template slot="text">
            <slot name="text" />
        </template>
        <template v-for="group in commandGroups">
            <SharpDropdownItem v-for="command in group" @click="handleCommandClicked(command)" :key="command.key">
                {{ command.label }}
                <div v-if="command.description" class="SharpCommandsDropdown__description mt-1">
                    {{ command.description }}
                </div>
            </SharpDropdownItem>
            <SharpDropdownSeparator />
        </template>
    </SharpDropdown>
</template>

<script>
    import SharpDropdown from '../dropdown/Dropdown';
    import SharpDropdownItem from '../dropdown/DropdownItem';
    import SharpDropdownSeparator from '../dropdown/DropdownSeparator';

    export default {
        name: 'SharpCommandsDropdown',

        components: {
            SharpDropdown,
            SharpDropdownItem,
            SharpDropdownSeparator,
        },

        props: {
            // 2D Array of command groups
            commands: {
                type: Array,
            }
        },

        computed: {
            commandGroups() {
                return this.commands.filter(group => group.length > 0);
            }
        },

        methods: {
            handleCommandClicked(command) {
                this.$emit('select', command);
            }
        }
    }
</script>
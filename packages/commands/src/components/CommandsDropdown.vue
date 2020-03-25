<template>
    <Dropdown class="SharpCommandsDropdown">
        <template slot="text">
            <slot name="text" />
        </template>
        <template v-for="group in commandGroups">
            <DropdownItem v-for="command in group" @click="handleCommandClicked(command)" :key="command.key">
                {{ command.label }}
                <div v-if="command.description" class="SharpCommandsDropdown__description mt-1">
                    {{ command.description }}
                </div>
            </DropdownItem>
            <DropdownSeparator />
        </template>
    </Dropdown>
</template>

<script>
    import { Dropdown, DropdownItem, DropdownSeparator } from 'sharp-ui';

    export default {
        name: 'SharpCommandsDropdown',

        components: {
            Dropdown,
            DropdownItem,
            DropdownSeparator,
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
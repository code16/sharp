<template>
    <Dropdown class="SharpCommandsDropdown">
        <template v-slot:text>
            <slot name="text" />
        </template>
        <template v-for="(group, i) in commandGroups">
            <template v-if="i > 0">
                <DropdownSeparator />
            </template>
            <template v-for="command in group">
                <DropdownItem @click="handleCommandClicked(command)" :key="command.key">
                    {{ command.label }}
                    <template v-if="command.description">
                        <div class="SharpCommandsDropdown__description mt-1">
                            {{ command.description }}
                        </div>
                    </template>
                </DropdownItem>
            </template>
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

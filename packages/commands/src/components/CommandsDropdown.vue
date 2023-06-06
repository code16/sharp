<template>
    <Dropdown class="SharpCommandsDropdown"
        :class="classes"
        :small="small"
        right
        v-bind="$attrs"
        v-on="$listeners"
    >
        <template v-slot:text>
            <slot name="text" />
        </template>
        <slot name="prepend" />
        <template v-for="(group, i) in commandGroups">
            <template v-if="i > 0">
                <DropdownSeparator />
            </template>
            <template v-for="command in group">
                <DropdownItem
                    @click="handleCommandClicked(command)"
                    :disabled="isDisabled(command)"
                    :key="command.key"
                >
                    {{ command.label }}
                    <template v-if="command.description">
                        <div class="SharpCommandsDropdown__description" :class="{ 'opacity-75': isDisabled(command) }">
                            {{ command.description }}
                        </div>
                    </template>
                </DropdownItem>
            </template>
        </template>
        <slot name="append" />
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
            },
            small: {
                type: Boolean,
                default: true,
            },
            hasState: Boolean,
            selecting: Boolean,
        },

        computed: {
            commandGroups() {
                return this.commands.filter(group => group.length > 0);
            },
            classes() {
                return {
                    'SharpCommandsDropdown--has-state': this.hasState
                }
            },
        },

        methods: {
            isDisabled(command) {
                return !this.selecting && command.instance_selection === 'required';
            },
            handleCommandClicked(command) {
                this.$emit('select', command);
            }
        }
    }
</script>

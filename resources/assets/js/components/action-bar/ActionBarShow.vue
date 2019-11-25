<template>
    <SharpActionBar>
        <template slot="left">
            <template v-if="showBackButton">
                <a :href="backUrl" class="SharpButton SharpButton--secondary-accent">
                    {{ l('action_bar.show.back_button') }}
                </a>
            </template>
        </template>
        <template slot="right">
            <template v-if="canEdit">
                <a :href="formUrl" class="SharpButton SharpButton--accent">
                    {{ l('action_bar.show.edit_button') }}
                </a>
            </template>
        </template>
        <template slot="extras-right">
            <div class="row mx-n1">
                <template v-if="hasState">
                    <div class="col-auto px-1">
                        <SharpDropdown class="SharpActionBar__actions-dropdown SharpActionBar__actions-dropdown--state" :disabled="!canChangeState">
                            <template slot="text">
                                <SharpStateIcon :color="state.color" />
                                <span class="text-truncate">{{ state.label }}</span>
                            </template>
                            <SharpDropdownItem
                                v-for="stateOptions in stateValues"
                                @click="handleStateChanged(stateOptions.value)"
                                :key="stateOptions.value"
                            >
                                <SharpStateIcon :color="stateOptions.color" />&nbsp;
                                {{ stateOptions.label }}
                            </SharpDropdownItem>
                        </SharpDropdown>
                    </div>
                </template>
                <template v-if="hasCommands">
                    <div class="col-auto px-1">
                        <SharpCommandsDropdown class="SharpActionBar__actions-dropdown SharpActionBar__actions-dropdown--commands"
                            :commands="commands"
                            @select="handleCommandSelected"
                        >
                            <div slot="text">
                                {{ l('entity_list.commands.instance.label') }}
                            </div>
                        </SharpCommandsDropdown>
                    </div>
                </template>
            </div>
        </template>
    </SharpActionBar>
</template>

<script>
    import { Localization } from "../../mixins";
    import SharpActionBar from './ActionBar';
    import SharpCommandsDropdown from '../commands/CommandsDropdown';
    import SharpDropdown from '../dropdown/Dropdown';
    import SharpDropdownItem from '../dropdown/DropdownItem';
    import SharpStateIcon from '../list/StateIcon';

    export default {
        mixins: [Localization],
        components: {
            SharpActionBar,
            SharpCommandsDropdown,
            SharpDropdown,
            SharpDropdownItem,
            SharpStateIcon,
        },
        props: {
            commands: Array,
            formUrl: String,
            backUrl: String,
            canEdit: Boolean,
            canChangeState: Boolean,
            showBackButton: Boolean,
            state: Object,
            stateValues: Array,
        },
        computed: {
            hasCommands() {
                return this.commands && this.commands.some(group => group && group.length > 0);
            },
            hasState() {
                return !!this.state;
            },
        },
        methods: {
            handleEditButtonClicked() {
                this.$emit('edit');
            },
            handleCommandSelected(command) {
                this.$emit('command', command);
            },
            handleStateChanged(state) {
                this.$emit('state-change', state);
            },
        }
    }
</script>
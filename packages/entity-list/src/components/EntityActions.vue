<script setup lang="ts">
    import { __ } from "@/utils/i18n";
    import { EntityList } from "../EntityList";
    import { Instance } from "../types";
    import { nextTick, ref } from "vue";
    import { CommandsDropdown } from "@sharp/commands";
    import { DropdownSeparator, DropdownItem, StateIcon, Dropdown } from "@sharp/ui";

    defineProps<{
        entityList: EntityList,
        showEntityState: Boolean,
        item: Instance,
        selecting: Boolean,
    }>();

    const emit = defineEmits(['state-change', 'command', 'delete']);
    const stateDropdown = ref();

    function onInstanceStateChange(state, instanceId) {
        emit('state-change', state);
    }
    function onInstanceCommand(command, instanceId) {
        emit('command', command);
    }
    function onDelete(instanceId) {
        emit('delete');
    }

    async function onStateDropdownClick() {
        await nextTick();
        stateDropdown.value.open();
    }
</script>

<template>
    <div class="SharpEntityList__actions">
        <div class="row align-items-center justify-content-end flex-nowrap gx-1">
            <template v-if="entityList.config.state && showEntityState">
                <div class="col-auto">
                    <Dropdown
                        toggle-class="btn--opacity-1 btn--outline-hover"
                        small
                        :show-caret="false"
                        outline
                        right
                        :disabled="!entityList.instanceCanUpdateState(item)"
                        :title="entityList.instanceStateValue(item)?.label ?? String(entityList.instanceState(item))"
                        ref="stateDropdown"
                    >
                        <template v-slot:text>
                            <StateIcon :color="entityList.instanceStateValue(item)?.color ?? '#fff'" />
                        </template>
                        <template v-for="stateValue in entityList.config.state.values" :key="stateValue.value">
                            <DropdownItem
                                :active="entityList.instanceState(item) === stateValue.value"
                                @click="onInstanceStateChange(stateValue.value, entityList.instanceId(item))"
                            >
                                <StateIcon class="me-1" :color="stateValue.color" style="vertical-align: -.125em" />
                                <span class="text-truncate">{{ stateValue.label }}</span>
                            </DropdownItem>
                        </template>
                    </Dropdown>
                </div>
            </template>

            <template v-if="
                entityList.instanceCommands(item)?.flat().length ||
                entityList.config.state && showEntityState ||
                !entityList.config.deleteHidden && entityList.instanceCanDelete(item)
            ">
                <div class="col-auto">
                    <CommandsDropdown
                        class="SharpEntityList__commands-dropdown"
                        outline
                        :commands="entityList.instanceCommands(item)"
                        :toggle-class="['p-1 commands-toggle', { 'opacity-50': selecting }]"
                        :show-caret="false"
                        @select="onInstanceCommand($event, entityList.instanceId(item))"
                    >
                        <template v-slot:text>
                            <!-- EllipsisVerticalIcon -- @heroicons/vue/20 -->
                            <svg class="d-block" width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                            </svg>
                        </template>
                        <template v-if="entityList.config.state && showEntityState" v-slot:prepend>
                            <DropdownItem
                                :disabled="!entityList.instanceCanUpdateState(item)"
                                @click.prevent="onStateDropdownClick"
                            >
                                <div class="row align-items-center gx-2 flex-nowrap">
                                    <div class="col-auto">
                                        <StateIcon :color="entityList.instanceStateValue(item)?.color ?? '#fff'" />
                                    </div>
                                    <div class="col">
                                        <div class="row gx-2">
                                            <template v-if="!entityList.instanceCanUpdateState(item)">
                                                <div class="col-auto">
                                                    {{ __('sharp::modals.entity_state.edit.title') }} :
                                                </div>
                                            </template>
                                            <div class="col-auto">
                                                {{ entityList.instanceStateValue(item)?.label ?? entityList.instanceState(item) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </DropdownItem>
                            <DropdownSeparator />
                        </template>
                        <template v-if="!entityList.config.deleteHidden && entityList.instanceCanDelete(item)" v-slot:append>
                            <template v-if="entityList.instanceCommands(item)?.flat().length">
                                <DropdownSeparator />
                            </template>
                            <DropdownItem link-class="text-danger" @click="onDelete(entityList.instanceId(item))">
                                {{ __('sharp::action_bar.form.delete_button') }}
                            </DropdownItem>
                        </template>
                    </CommandsDropdown>
                </div>
            </template>
        </div>
    </div>
</template>

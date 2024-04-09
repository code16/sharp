<script setup lang="ts">
    import { __ } from "@/utils/i18n.js";
    import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuSeparator } from "@/components/ui/dropdown-menu";

    import { CommandData } from "@/types/index.js";

    const props = defineProps<{
        commands: CommandData[][],
        selecting?: boolean,
    }>();

    defineEmits(['select']);

    function requiresSelection(command) {
        return !props.selecting && command.instance_selection === 'required';
    }
</script>

<template>
    <template v-for="(group, i) in commands?.filter(group => group.length > 0)">
        <template v-if="i > 0">
            <DropdownMenuSeparator />
        </template>
        <DropdownMenuGroup>
            <template v-for="command in group" :key="command.key">
                <DropdownMenuItem
                    @click="$emit('select', command)"
                    :disabled="requiresSelection(command)"
                    :title="requiresSelection(command) ? __('sharp::entity_list.commands.needs_selection_message') : null"
                >
                    <!--                    todo tooltip (from title attribute) -->
                    {{ command.label }}
                    <template v-if="command.description">
                        <div :class="{ 'opacity-75': requiresSelection(command) }">
                            {{ command.description }}
                        </div>
                    </template>
                </DropdownMenuItem>
            </template>
        </DropdownMenuGroup>
    </template>
</template>

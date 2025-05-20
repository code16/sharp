<script setup lang="ts">
    import { __ } from "@/utils/i18n.js";
    import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuSeparator } from "@/components/ui/dropdown-menu";

    import { CommandData } from "@/types";
    import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";
    import { useBreakpoints } from "@/composables/useBreakpoints";
    import { showAlert } from "@/utils/dialogs";

    const props = defineProps<{
        commands: CommandData[][],
        selecting?: boolean,
    }>();

    defineEmits(['select']);

    function requiresSelection(command: CommandData) {
        return !props.selecting && command.instanceSelection === 'required';
    }

    const breakpoints = useBreakpoints();
</script>

<template>
    <template v-for="(group, i) in commands?.filter(group => group.length > 0)">
        <template v-if="i > 0">
            <DropdownMenuSeparator class="first:hidden" />
        </template>
        <DropdownMenuGroup class="max-w-sm">
            <TooltipProvider>
                <template v-for="command in group" :key="command.key">
                    <Tooltip :delay-duration="0">
                        <component :is="requiresSelection(command) ? TooltipTrigger : 'div'"
                            @touchend="requiresSelection(command) && showAlert(__('sharp::entity_list.commands.needs_selection_message'), { highlightElement: $event.target })"
                            as="div"
                        >
                            <DropdownMenuItem
                                @click="$emit('select', command)"
                                :disabled="requiresSelection(command)"
                            >
                                <div>
                                    {{ command.label }}
                                    <template v-if="command.description">
                                        <div class="text-xs text-muted-foreground">
                                            {{ command.description }}
                                        </div>
                                    </template>
                                </div>
                            </DropdownMenuItem>
                        </component>
                        <TooltipContent class="max-w-sm" :side="breakpoints.md ? 'left' : 'bottom'">
                            {{ __('sharp::entity_list.commands.needs_selection_message') }}
                        </TooltipContent>
                    </Tooltip>
                </template>
            </TooltipProvider>
        </DropdownMenuGroup>
    </template>
</template>

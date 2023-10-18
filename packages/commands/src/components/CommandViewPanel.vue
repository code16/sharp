<script setup lang="ts">
    import { CommandManager } from "../CommandManager";

    defineProps<{
        commands: CommandManager,
    }>();
</script>

<template>
<!--    TODO integration -->
    <div>
        <div class="fixed inset-0 z-[900]"
            v-show="commands.state.currentCommandResponse?.action === 'view'"
            @click="commands.finish()"
        ></div>
        <transition
            enter-class="SharpViewPanel--collapsed"
            enter-active-class="SharpViewPanel--expanding"
            enter-to-class="SharpViewPanel--expanded"
            leave-class="SharpViewPanel--expanded"
            leave-active-class="SharpViewPanel--collapsing"
            leave-to-class="SharpViewPanel--collapsed"
        >
            <template v-if="commands.state.currentCommandResponse?.action === 'view'">
                <div class="fixed top-0 left-0 bottom-0 w-5/6 bg-white z-[1000]">
                    <iframe
                        :srcdoc="commands.state.currentCommandResponse.html"
                        sandbox="allow-forms allow-scripts allow-same-origin allow-popups allow-modals allow-downloads"
                    ></iframe>
                </div>
            </template>
        </transition>
    </div>
</template>

import { useColorMode as vueuseColorMode } from "@vueuse/core";
import { Ref } from "vue";

type UseColorModeReturn<WithAuto extends boolean> = WithAuto extends true
    ? Ref<'light' | 'dark' | 'auto'>
    : Ref<'light' | 'dark'>;

export function useColorMode<WithAuto extends boolean = false>(withAuto?: WithAuto): UseColorModeReturn<WithAuto> {
    const mode = vueuseColorMode();
    return (withAuto ? mode.store : mode) as UseColorModeReturn<WithAuto>;
}

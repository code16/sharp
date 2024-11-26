import { breakpointsTailwind, useBreakpoints as baseUseBreakpoints, UseBreakpointsReturn } from "@vueuse/core";
import { Reactive, reactive } from "vue";

export function useBreakpoints(): Reactive<UseBreakpointsReturn<keyof typeof breakpointsTailwind>> {
    return reactive(baseUseBreakpoints(breakpointsTailwind)) as any;
}

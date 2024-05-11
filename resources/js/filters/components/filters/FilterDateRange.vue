<script setup lang="ts">
    // import FilterControl from '../FilterControl.vue';
    import { parseDate, CalendarDate, fromDate, getLocalTimeZone, toCalendarDate  } from '@internationalized/date';
    import { DateRangeFilterData } from "@/types";
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import { Button } from "@/components/ui/button";
    import { CalendarIcon, ChevronDown, PlusCircle } from "lucide-vue-next";
    import { RangeCalendar } from "@/components/ui/range-calendar";
    import { reactive, ref, watch } from "vue";
    import { Separator } from "@/components/ui/separator";
    import { Badge } from "@/components/ui/badge";
    import { Input } from "@/components/ui/input";
    import { __ } from "@/utils/i18n";
    import { Label } from "@/components/ui/label";
    import FilterSelectValue from "@/filters/components/filters/FilterSelectValue.vue";
    import FilterDateRangeValue from "@/filters/components/filters/FilterDateRangeValue.vue";
    import { cn } from "@/utils/cn";

    const props = defineProps<{
        value: DateRangeFilterData['value'],
        filter: Omit<DateRangeFilterData, 'value'>,
        valuated: boolean,
        disabled?: boolean,
        inline?: boolean,
    }>();

    const emit = defineEmits(['input']);

    const localValue = ref<{ start?: CalendarDate, end?: CalendarDate, preset?: string }>();
    const inputs = reactive({
        start: '',
        end: ''
    });
    const edited = reactive({ start: false, end: false, count: 0 });
    const open = ref(false);

    watch(() => props.value, update);

    function update() {
        localValue.value = props.value ? {
            start: parseDate(props.value.start),
            end: parseDate(props.value.end),
            preset: props.value.preset,
        } : {};
        inputs.start = localValue.value.start?.toString();
        inputs.end = localValue.value.end?.toString();
        edited.count++;
    }

    function onOpen() {
        update();
    }

    function onPresetSelected(preset: DateRangeFilterData['presets'][number]) {
        open.value = false;
        emit('input', {
            preset: preset.key,
        });
    }

    function onSubmit() {
        open.value = false;
        emit('input', localValue.value.start ? {
            start: localValue.value.start.toString(),
            end: localValue.value.end.toString(),
        } : null);
    }

    function onCalendarChange() {
        inputs.start = localValue.value.start?.toString() ?? '';
        inputs.end = localValue.value.end?.toString() ?? '';
        edited.start = true;
        edited.end = true;
    }

    function onResetClick() {
        open.value = false;
        emit('input', null);
    }

    function onStartChange() {
        edited.count++;
        edited.start = true;
        localValue.value.start = inputs.start ? parseDate(inputs.start) : null;
        if(!edited.end) {
            inputs.end = inputs.start;
            localValue.value.end = localValue.value.start;
        }
    }

    function onEndChange() {
        edited.count++;
        edited.end = true;
        localValue.value.end = inputs.end ? parseDate(inputs.end) : null;
        if(!edited.start) {
            inputs.start = inputs.end;
            localValue.value.start = localValue.value.end;
        }
    }
</script>

<template>
    <div>
        <Label v-if="!inline">
            {{ filter.label }}
        </Label>

        <Popover v-model:open="open" @update:open="$event && onOpen()" modal>
            <PopoverTrigger as-child>
                <template v-if="inline">
                    <Button variant="outline" size="sm" class="h-8 border-dashed transition-shadow shadow-sm data-[state=open]:shadow-md" :disabled="disabled">
                        <CalendarIcon class="mr-2 h-4 w-4 stroke-[1.25]" />
                        {{ filter.label }}
                        <template v-if="value?.start">
                            <Separator orientation="vertical" class="mx-2 h-4" />
                            <FilterDateRangeValue :filter="filter" :value="value" inline />
                        </template>
                    </Button>
                </template>
                <template v-else>
                    <Button
                        class="mt-2 w-full text-left justify-between font-normal"
                        variant="outline"
                        size="sm"
                        :disabled="disabled"
                    >
                        <template v-if="value?.start">
                            <FilterDateRangeValue :filter="filter" :value="value" />
                        </template>
                        <template v-else>
                            <span class="text-muted-foreground">
                                {{ __('sharp::form.multiselect.placeholder') }}
                            </span>
                        </template>
                        <CalendarIcon class="ml-2 h-4 w-4 stroke-[1.25]" />
                    </Button>
                </template>
            </PopoverTrigger>
            <PopoverContent :class="cn('w-auto p-0', !inline ? 'w-[--radix-popover-trigger-width]' : '')">
                <div class="flex">
                    <template v-if="filter.presets?.length">
                        <div class="flex flex-col shrink-0 p-3">
                            <template v-for="preset in filter.presets">
                                <Button
                                    class="text-left justify-start"
                                    :class="{ 'bg-accent text-accent-foreground': preset.key === localValue.preset }"
                                    size="sm"
                                    variant="ghost"
                                    @click="onPresetSelected(preset)"
                                >
                                    {{ preset.label }}
                                </Button>
                            </template>
                        </div>
                    </template>
                    <div class="flex-1">
                        <RangeCalendar
                            class="hidden md:block"
                            v-model="localValue"
                            :number-of-months="2"
                            :locale="window.navigator.language"
                            @update:start-value="(startDate) => localValue.start = startDate"
                            @update:model-value="onCalendarChange"
                            :key="edited.count"
                        />
                        <div class="grid gap-4 p-3" key="footer">
                            <div class="grid md:grid-cols-2 gap-4">
                                <Input class="block" type="date"
                                    v-model="inputs.start"
                                    @update:model-value="onStartChange"
                                />
                                <Input class="block" type="date"
                                    v-model="inputs.end"
                                    @update:model-value="onEndChange"
                                />
                            </div>
                            <div class="flex flex-col md:flex-row justify-end gap-3 pt-0">
                                <template v-if="localValue.end">
                                    <Button variant="outline" @click="onResetClick">
                                        {{ __('sharp::filters.select.reset') }}
                                    </Button>
                                </template>
                                <Button class="max-md:h-8" @click="onSubmit">
                                    {{ __('sharp::filters.daterange.confirm') }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </PopoverContent>
        </Popover>
    </div>
</template>

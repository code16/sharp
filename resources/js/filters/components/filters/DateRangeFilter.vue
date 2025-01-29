<script setup lang="ts">
    import { parseDate, CalendarDate } from '@internationalized/date';
    import { DateRangeFilterData } from "@/types";
    import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
    import { Button } from "@/components/ui/button";
    import { CalendarIcon } from "lucide-vue-next";
    import { RangeCalendar } from "@/components/ui/range-calendar";
    import { reactive, ref, watch } from "vue";
    import { Separator } from "@/components/ui/separator";
    import { Input } from "@/components/ui/input";
    import { __ } from "@/utils/i18n";
    import { Label } from "@/components/ui/label";
    import DateRangeFilterValue from "@/filters/components/filters/DateRangeFilterValue.vue";
    import { cn } from "@/utils/cn";
    import { FilterEmits, FilterProps } from "@/filters/types";

    const props = defineProps<FilterProps<DateRangeFilterData>>();
    const emit = defineEmits<FilterEmits<DateRangeFilterData, { start: string, end: string } | { preset: string } | null>>();

    const localValue = ref<{ start: CalendarDate | null, end: CalendarDate | null }>();
    const inputs = reactive({ start: '', end: '' });
    const renderKey = ref(0);
    const open = ref(false);

    watch(() => props.value, () => update());

    function update() {
        localValue.value = {
            start: props.value?.start ? parseDate(props.value.start) : null,
            end: props.value?.end ? parseDate(props.value.end) : null,
        };
        inputs.start = localValue.value.start?.toString();
        inputs.end = localValue.value.end?.toString();
        renderKey.value++;
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
        emit('input', localValue.value.start && localValue.value.end ? {
            start: localValue.value.start.toString(),
            end: localValue.value.end.toString(),
        } : null);
    }

    function onCalendarChange() {
        inputs.start = localValue.value.start?.toString() ?? '';
        inputs.end = localValue.value.end?.toString() ?? '';
    }

    function onResetClick() {
        open.value = false;
        emit('input', null);
    }

    function onStartChange() {
        renderKey.value++;
        localValue.value.start = inputs.start ? parseDate(inputs.start) : null;
        if(!inputs.end) {
            inputs.end = inputs.start;
            localValue.value.end = localValue.value.start;
        }
    }

    function onEndChange() {
        renderKey.value++;
        localValue.value.end = inputs.end ? parseDate(inputs.end) : null;
        if(!inputs.start) {
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

        <Popover v-model:open="open" @update:open="$event && onOpen()" :modal="!inline">
            <PopoverTrigger as-child>
                <template v-if="inline">
                    <Button
                        class="h-8 gap-2 transition-shadow data-[state=open]:shadow-md"
                        variant="outline" size="sm"
                        :disabled="disabled"
                        role="combobox"
                        aria-autocomplete="none"
                        :aria-label="filter.label"
                    >
                        <CalendarIcon class="h-4 w-4 opacity-50" />
                        <span aria-hidden="true">
                            {{ filter.label }}
                        </span>
                        <template v-if="props.value && 'start' in props.value">
                            <Separator orientation="vertical" class="h-4" />
                            <DateRangeFilterValue v-bind="props" />
                        </template>
                    </Button>
                </template>
                <template v-else>
                    <Button
                        class="mt-2 w-full text-left justify-between font-normal"
                        variant="outline"
                        size="sm"
                        :disabled="disabled"
                        role="combobox"
                        aria-autocomplete="none"
                        :aria-label="filter.label"
                    >
                        <template v-if="props.value && 'start' in props.value">
                            <DateRangeFilterValue v-bind="props" />
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
            <PopoverContent :class="cn('w-auto p-0', !inline ? 'w-[--reka-popover-trigger-width]' : '')">
                <div class="flex">
                    <template v-if="filter.presets?.length">
                        <div class="flex flex-col shrink-0 p-3">
                            <template v-for="preset in filter.presets">
                                <Button
                                    class="text-left justify-start"
                                    :class="{ 'bg-accent text-accent-foreground': preset.key === props.value?.preset }"
                                    size="sm"
                                    variant="ghost"
                                    @click="onPresetSelected(preset)"
                                >
                                    {{ preset.label }}
                                </Button>
                            </template>
                        </div>
                    </template>
                    <div class="flex-1 min-w-0">
                        <RangeCalendar
                            :class="!inline ? 'hidden' : 'hidden md:block'"
                            v-model="localValue"
                            :number-of-months="2"
                            :locale="window.navigator.language"
                            :week-starts-on="props.filter.mondayFirst ? 1 : 0"
                            @update:start-value="(startDate) => localValue.start = startDate as CalendarDate"
                            @update:model-value="onCalendarChange"
                            :key="renderKey"
                        />
                        <div class="grid grid-cols-1 gap-4 p-3" key="footer">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <Input class="block" type="date"
                                    v-model="inputs.start"
                                    @update:model-value="onStartChange"
                                    :aria-label="__('sharp::form.daterange.start_placeholder')"
                                />
                                <Input class="block" type="date"
                                    v-model="inputs.end"
                                    @update:model-value="onEndChange"
                                    :aria-label="__('sharp::form.daterange.end_placeholder')"
                                />
                            </div>
                            <div class="flex flex-col md:flex-row justify-end gap-3 pt-0">
                                <template v-if="localValue.end">
                                    <Button class="max-md:h-8" variant="outline" @click="onResetClick">
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

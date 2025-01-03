<script setup lang="ts">
import { Input } from "@/components/ui/input";
import { FormFieldEmits, FormFieldProps } from "@/form/types";
import { FormDateFieldData } from "@/types";
import {
    parseDate,
    parseDateTime,
    toCalendarDate,
    toCalendarDateTime,
    parseTime,
    today,
    getLocalTimeZone,
    toTime,
    Time,
    CalendarDate
} from '@internationalized/date';
import { computed, ref, watch } from "vue";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Command, CommandGroup, CommandItem, CommandList } from "@/components/ui/command";
import { vScrollIntoView } from "@/directives/scroll-into-view";
import {
    CalendarCell, CalendarCellTrigger,
    CalendarGrid,
    CalendarGridBody,
    CalendarGridHead,
    CalendarGridRow,
    CalendarHeadCell, CalendarHeader, CalendarHeading, CalendarNextButton, CalendarPrevButton
} from "@/components/ui/calendar";
import { Separator } from "@/components/ui/separator";
import { CalendarIcon, Clock, X } from "lucide-vue-next";
import FormFieldLayout from "@/form/components/FormFieldLayout.vue";
import { CalendarRoot } from "reka-ui";
import { createYear, createYearRange, toDate } from 'reka-ui/date'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Button } from "@/components/ui/button";

const props = defineProps<FormFieldProps<FormDateFieldData>>();
const emit = defineEmits<FormFieldEmits<FormDateFieldData>>();

const dateLocale = Intl.DateTimeFormat().resolvedOptions().locale;
const open = ref(false);
const isTouch = ref(false);

const calendarDateValue = computed(() => {
    if(props.value && props.field.hasDate) {
        return props.field.hasTime
            ? toCalendarDate(parseDateTime(props.value))
            : parseDate(props.value);
    }
    return null;
});

const calendarViewingDate = ref<CalendarDate>();

watch(open, () => {
    if(open.value) {
        calendarViewingDate.value = calendarDateValue.value ?? today(getLocalTimeZone());
    }
});

const inputValue = computed(() => {
    if(props.field.hasDate && props.field.hasTime) {
        // remove seconds of YYYY-MM-DDTHH:mm:ss
        return props.value?.substring(0, 16);
    }
    if(props.field.hasTime) {
        // remove seconds of HH:mm:ss
        return props.value?.substring(0, 5);
    }
    return props.value;
});

function onDateInput(inputValue: string) {
    emit('input', inputValue);
}

function onCalendarDateChange(value: CalendarDate) {
    if(props.field.hasTime) {
        emit('input', props.value
            ? parseDateTime(props.value).set(value).toString()
            : toCalendarDateTime(value, parseTime(props.field.minTime)).toString()
        );
    } else {
        emit('input', value.toString());
    }
}

const hours = computed(() => {
    const minTime = parseTime(props.field.minTime);
    const maxTime = parseTime(props.field.maxTime);

    return Array.from({ length: maxTime.hour - minTime.hour + 1 })
        .map((_, i) => minTime.hour + i)
        .map(String);
});

const minutes = computed(() => {
    const minTime = parseTime(props.field.minTime);
    const maxTime = parseTime(props.field.maxTime);
    const currentHour = props.value && props.field.hasTime
        ? props.field.hasDate ? parseDateTime(props.value).hour : parseTime(props.value).hour
        : null;
    return Array.from({ length: 60 / props.field.stepTime })
        .map((_, i) => i * props.field.stepTime)
        .filter(minutes => {
            if(currentHour === minTime.hour) {
                return minutes >= minTime.minute;
            }
            if(currentHour === maxTime.hour) {
                return minutes <= maxTime.minute;
            }
            return true;
        })
        .map(String);
});

const hourValue = computed(() => {
    if(props.value) {
        return props.field.hasDate
            ? toTime(parseDateTime(props.value)).hour.toString()
            : parseTime(props.value).hour.toString();
    }
    return '';
})
function onHourChange(hour: string) {
    if(props.field.hasDate) {
        emit('input', props.value
            ? parseDateTime(props.value).set({ hour: Number(hour) }).toString()
            : toCalendarDateTime(today(getLocalTimeZone())).set({ hour: Number(hour) }).toString()
        );
    } else {
        emit('input', props.value
            ? parseTime(props.value).set({ hour: Number(hour) }).toString()
            : new Time(Number(hour), 0).toString()
        );
    }
}

const minuteValue = computed(() => {
    if(props.value) {
        return props.field.hasDate
            ? toTime(parseDateTime(props.value)).minute.toString()
            : parseTime(props.value).minute.toString();
    }
    return '';
})
function onMinuteChange(minute: string) {
    if(props.field.hasDate) {
        emit('input', props.value
            ? parseDateTime(props.value).set({ minute: Number(minute) }).toString()
            : toCalendarDateTime(today(getLocalTimeZone())).set({ minute: Number(minute) }).toString()
        );
    } else {
        emit('input', props.value
            ? parseTime(props.value).set({ minute: Number(minute) }).toString()
            : new Time(parseTime(props.field.minTime).hour, Number(minute)).toString()
        );
    }
}
</script>

<template>
    <Popover v-model:open="open" :modal="false">
        <FormFieldLayout v-bind="props" v-slot="{ id, ariaDescribedBy }">
            <div class="relative">
                <PopoverTrigger as-child>
                    <Input
                        :id="id"
                        class="pl-10 min-w-full appearance-none text-left [&::-webkit-calendar-picker-indicator]:hidden [&::-webkit-date-and-time-value]:text-left"
                        :class="{ 'pr-10 [@supports_not_selector(::-webkit-calendar-picker-indicator)]:pr-3': props.value }"
                        :type="
                            props.field.hasDate && props.field.hasTime ? 'datetime-local'
                            : props.field.hasTime ? 'time'
                            : 'date'
                        "
                        :disabled="props.field.readOnly"
                        :step="props.field.hasTime ? props.field.stepTime * 60 : null"
                        :aria-describedby="ariaDescribedBy"
                        :model-value="inputValue"
                        @update:model-value="onDateInput"
                        @touchstart="isTouch = true"
                        @click.prevent="open = !isTouch"
                        ref="input"
                    />
                </PopoverTrigger>
                <div class="absolute right-px rounded-md top-px w-10 bottom-px bg-background hidden [@supports_not_selector(::-webkit-calendar-picker-indicator)]:block"
                    @touchstart="isTouch = true"
                    @click="($refs.input.$el as HTMLInputElement).focus(); open = !isTouch"
                ></div>
                <template v-if="props.value">
                    <Button class="absolute opacity-50 hover:opacity-100 right-px h-[2.375rem] top-1/2 -translate-y-1/2"  variant="ghost" size="icon" @click="$emit('input', null)">
                        <X class="size-4" />
                    </Button>
                </template>
                <template v-if="props.field.hasDate">
                    <CalendarIcon class="absolute top-1/2 -translate-y-1/2 left-3.5 size-4 text-muted-foreground pointer-events-none" />
                </template>
                <template v-else>
                    <Clock class="absolute top-1/2 -translate-y-1/2 left-3.5 size-4 text-muted-foreground pointer-events-none" />
                </template>
            </div>
        </FormFieldLayout>

        <PopoverContent
            class="flex p-0 w-auto overflow-hidden"
            @open-auto-focus.prevent
            :avoid-collisions="false"
            align="start"
        >
            <template v-if="props.field.hasDate">
                <CalendarRoot
                    v-slot="{ date, grid, weekDays }"
                    :model-value="calendarDateValue"
                    v-model:placeholder="calendarViewingDate"
                    :locale="dateLocale"
                    :week-starts-on="props.field.mondayFirst ? 1 : 0"
                    @update:model-value="onCalendarDateChange"
                    class="p-3"
                >
                    <CalendarHeader class="gap-2">
                        <CalendarPrevButton class="shrink-0" />
                        <CalendarHeading class="flex w-full items-center gap-2">
                            <Select
                                :model-value="date.month.toString()"
                                @update:model-value="(month) => { calendarViewingDate = calendarViewingDate.set({ month: Number(month) }) }"
                            >
                                <SelectTrigger class="flex-1 w-auto h-7 text-left gap-1 px-2 py-1" aria-label="Select month">
                                    <SelectValue class="capitalize min-w-20" />
                                </SelectTrigger>
                                <SelectContent :align-offset="-28" :body-lock="false">
                                    <SelectItem
                                        class="capitalize pr-8"
                                        v-for="month in createYear({ dateObj: date })"
                                        :key="month.toString()"
                                        :value="month.month.toString()"
                                    >
                                        {{ new Intl.DateTimeFormat(dateLocale, { month: 'long' }).format(toDate(month)) }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <Select
                                :model-value="date.year.toString()"
                                @update:model-value="(year) => { calendarViewingDate = calendarViewingDate.set({ year: Number(year) }) }"
                            >
                                <SelectTrigger class="w-auto h-7 gap-1 text-left px-2 py-1" aria-label="Select year">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent :align-offset="-28" :body-lock="false">
                                    <SelectItem
                                        v-for="yearValue in createYearRange({
                                            start: new CalendarDate(date.year - 100, 1, 1),
                                            end: new CalendarDate(date.year + 100, 1, 1)
                                        })"
                                        :key="yearValue.toString()" :value="yearValue.year.toString()"
                                    >
                                        {{ yearValue.year }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </CalendarHeading>
                        <CalendarNextButton class="shrink-0" />
                    </CalendarHeader>

                    <div class="flex flex-col space-y-4 pt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
                        <CalendarGrid v-for="month in grid" :key="month.value.toString()">
                            <CalendarGridHead>
                                <CalendarGridRow>
                                    <CalendarHeadCell
                                        v-for="day in weekDays" :key="day"
                                    >
                                        {{ day }}
                                    </CalendarHeadCell>
                                </CalendarGridRow>
                            </CalendarGridHead>
                            <CalendarGridBody class="grid">
                                <CalendarGridRow v-for="(weekDates, index) in month.rows" :key="`weekDate-${index}`" class="mt-2 w-full">
                                    <CalendarCell
                                        v-for="weekDate in weekDates"
                                        :key="weekDate.toString()"
                                        :date="weekDate"
                                    >
                                        <CalendarCellTrigger
                                            :day="weekDate"
                                            :month="month.value"
                                        />
                                    </CalendarCell>
                                </CalendarGridRow>
                            </CalendarGridBody>
                        </CalendarGrid>
                    </div>
                </CalendarRoot>
            </template>

            <template v-if="props.field.hasDate && props.field.hasTime">
                <Separator class="h-auto mr-1" orientation="vertical" />
            </template>

            <template v-if="props.field.hasTime">
                <div class="flex p-1" :class="props.field.hasDate ? 'max-h-[330px]' : 'max-h-[250px]'">
                    <Command class="w-auto rounded-none" :model-value="hourValue" @update:model-value="onHourChange">
                        <CommandList class="max-h-none pr-1">
                            <CommandGroup class="w-12 p-0 rounded-sm">
                                <template v-for="hour in hours">
                                    <CommandItem
                                        class="data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                                        :value="hour"
                                        :autofocus="false"
                                    >
                                        {{ hour.padStart(2, '0') }}
                                    </CommandItem>
                                </template>
                            </CommandGroup>
                        </CommandList>
                    </Command>
                    <Command class="ml-1 w-auto rounded-none" :model-value="minuteValue" @update:model-value="onMinuteChange">
                        <CommandList class="max-h-none">
                            <CommandGroup class="w-12 p-0">
                                <template v-for="minute in minutes">
                                    <CommandItem
                                        class="data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                                        :value="minute"
                                        :autofocus="false"
                                    >
                                        {{ minute.padStart(2, '0') }}
                                    </CommandItem>
                                </template>
                            </CommandGroup>
                        </CommandList>
                    </Command>
                </div>
            </template>
        </PopoverContent>
    </Popover>
</template>

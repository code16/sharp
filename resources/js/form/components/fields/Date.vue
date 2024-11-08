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
import { computed, Directive, ref } from "vue";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Command, CommandGroup, CommandItem, CommandList } from "@/components/ui/command";
import { vScrollIntoView } from "@/directives/scroll-into-view";
import { Calendar } from "@/components/ui/calendar";
import { Separator } from "@/components/ui/separator";

const props = defineProps<FormFieldProps<FormDateFieldData>>();
const emit = defineEmits<FormFieldEmits<FormDateFieldData>>();

const calendarDateValue = computed(() => {
    if(props.value) {
        return props.field.hasTime
            ? toCalendarDate(parseDateTime(props.value))
            : parseDate(props.value);
    }
    return null;
});

function onCalendarDateChange(value: CalendarDate) {
    if(props.field.hasTime) {
        emit('input', props.value
            ? parseDateTime(props.value).set(value).toString()
            : toCalendarDateTime(value).toString()
        );
    } else {
        emit('input', value.toString());
    }
}

const open = ref(false);
function onDateInput(inputValue: string) {
    if(props.field.hasDate && props.field.hasTime) {
        // emit('input', props.value
        //     ? parseDateTime(props.value).set(parseDate(inputValue)).toString()
        //     : toCalendarDateTime(parseDate(inputValue)).toString()
        // );
        emit('input', inputValue);
    } else {
        emit('input', inputValue);
    }
}

// const timeValue = computed(() => {
//     return props.field.hasDate && props.value
//         ? toTime(parseDateTime(props.value)).toString()
//         : props.value;
// });
// function onTimeInput(inputValue: string) {
//     if(props.field.hasDate) {
//         emit('input', props.value
//             ? parseDateTime(props.value).set(parseTime(inputValue)).toString()
//             : toCalendarDateTime(today(getLocalTimeZone()), parseTime(inputValue)).toString()
//         );
//     } else {
//         emit('input', inputValue);
//     }
// }
const timeSelectValue = computed(() => {
    return props.field.hasDate && props.value
        ? toTime(parseDateTime(props.value)).toString().slice(0, 5)
        : props.value?.slice(0, 5);
});
const timeSelectItems = computed(() => {
    const [minHours, minMinutes] = props.field.minTime.split(':').map(Number);
    const [maxHours, maxMinutes] = props.field.maxTime.split(':').map(Number);
    let current = new Date(0, 0, 0, minHours, minMinutes);
    const end = new Date(0, 0, 0, maxHours, maxMinutes);
    const items = [];
    while (current <= end) {
        items.push(`${String(current.getHours()).padStart(2, '0')}:${String(current.getMinutes()).padStart(2, '0')}`);
        const minutes = Math.round((current.getMinutes() + props.field.stepTime) / props.field.stepTime) * props.field.stepTime;
        if(minutes >= 60) {
            current.setHours(current.getHours() + 1, 0);
        } else {
            current.setHours(current.getHours(), minutes);
        }
    }
    return items;
});
function onTimeSelectChange(selectValue: string) {
    if(props.field.hasDate) {
        emit('input', props.value
            ? parseDateTime(props.value).set(parseTime(selectValue)).toString()
            : toCalendarDateTime(today(getLocalTimeZone()), parseTime(selectValue)).toString()
        );
    } else {
        emit('input', selectValue);
    }
}

function itemValue(fields: { hour?: number, minute?: number }) {
    const time = props.field.hasDate && props.value
        ? toTime(parseDateTime(props.value))
        : parseTime(props.value || '00:00');
    return time.set(fields).toString().slice(0, 5);
}

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
        <div class="relative">
            <PopoverTrigger as-child>
                <Input
                    class="[&::-webkit-calendar-picker-indicator]:hidden"
                    :type="
                        props.field.hasDate && props.field.hasTime ? 'datetime-local'
                        : props.field.hasTime ? 'time'
                        : 'date'
                    "
                    :model-value="props.value"
                    @update:model-value="onDateInput"
                    @click="open =true"
                    ref="input"
                />
            </PopoverTrigger>
            <div class="absolute right-px rounded-md top-px w-10 bottom-px bg-white hidden [@supports_not_selector(::-webkit-calendar-picker-indicator)]:block"
                @click="($refs.input.$el as HTMLInputElement).focus(); open = true"
            ></div>
        </div>

        <PopoverContent
            class="flex p-0 w-auto overflow-hidden"
            @open-auto-focus.prevent
            :avoid-collisions="false"
            align="start"
        >
            <Calendar
                :model-value="calendarDateValue"
                :locale="window.navigator.language"
                @update:model-value="onCalendarDateChange"
            />
            <Separator class="h-auto mx-2" orientation="vertical"></Separator>
            <div class="flex">
                <Command class="w-auto rounded-none" :model-value="hourValue" @update:model-value="onHourChange">
                    <CommandList class="max-h-[320px] scroll-py-1">
                        <CommandGroup class="w-14">
                            <template v-for="item in new Set(timeSelectItems.map(i => i.split(':')[0].replace(/^0/, '')))">
                                <CommandItem
                                    class="data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                                    :value="item"
                                    v-scroll-into-view="item === hourValue"
                                >
                                    {{ item.padStart(2, '0') }}
                                </CommandItem>
                            </template>
                        </CommandGroup>
                    </CommandList>
                </Command>
                <Command class="w-auto rounded-none" :model-value="minuteValue" @update:model-value="onMinuteChange">
                    <CommandList class="max-h-[320px] scroll-py-1">
                        <CommandGroup class="w-14">
                            <template v-for="item in new Set(timeSelectItems.map(i => i.split(':')[1].replace(/^0/, '')))">
                                <CommandItem
                                    class="data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                                    :value="item"
                                    v-scroll-into-view="item === minuteValue"
                                >
                                    {{ item.padStart(2, '0') }}
                                </CommandItem>
                            </template>
                        </CommandGroup>
                    </CommandList>
                </Command>
            </div>
        </PopoverContent>
    </Popover>
</template>

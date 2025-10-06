

export function getWeekStartsOn(mondayFirst: boolean) {
    return mondayFirst ? 0 : 6;
}

export function getDefaultDateLocale() {
    return Intl.DateTimeFormat().resolvedOptions().locale;
}

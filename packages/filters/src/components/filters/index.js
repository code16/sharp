import FilterDateRange from './FilterDateRange.vue';
import FilterSelect from './FilterSelect.vue';
import FilterCheck from './FilterCheck.vue';

export function filterByType(type) {
    if(type === 'select') {
        return FilterSelect;
    } else if(type === 'daterange') {
        return FilterDateRange;
    } else if(type === 'check') {
        return FilterCheck;
    }
}

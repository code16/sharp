import FilterDateRange from './FilterDateRange';
import FilterSelect from './FilterSelect';
import FilterCheck from './FilterCheck';

export function filterByType(type) {
    if(type === 'select') {
        return FilterSelect;
    } else if(type === 'daterange') {
        return FilterDateRange;
    } else if(type === 'check') {
        return FilterCheck;
    }
}

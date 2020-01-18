import FilterDateRange from './FilterDateRange';
import FilterSelect from './FilterSelect';

export function filterByType(type) {
    if(type === 'select') {
        return FilterSelect;
    } else if(type === 'daterange') {
        return FilterDateRange;
    }
}
import FilterDateRange from './FilterDateRange';
import FilterSelect from './FilterSelect';
import FilterCheckbox from './FilterCheckbox';

export function filterByType(type) {
    if(type === 'select') {
        return FilterSelect;
    } else if(type === 'daterange') {
        return FilterDateRange;
    } else if(type === 'checkbox') {
        return FilterCheckbox;
    }
}

import SharpFilterDateRange from './FilterDateRange';
import SharpFilterSelect from './FilterSelect';

export function filterByType(type) {
    if(type === 'select') {
        return SharpFilterSelect;
    } else if(type === 'daterange') {
        return SharpFilterDateRange;
    }
}

export {
    SharpFilterSelect,
    SharpFilterDateRange,
}
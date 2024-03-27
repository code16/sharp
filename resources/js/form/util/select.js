

export function isSelected(option, value) {
    if(option.id == null || value == null) {
        return false;
    }
    // noinspection EqualityComparisonWithCoercionJS
    return option.id == value;
}

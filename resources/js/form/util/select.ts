

export function isSelected(option, value) {
    if(option.id == null || value == null) {
        return false;
    }
    return option.id == value;
}

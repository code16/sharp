
let count = 0;

export function useId(prefix: string) {
    return `${prefix}-${count++}`;
}

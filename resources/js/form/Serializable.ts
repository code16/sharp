


export class Serializable {
    localValue: any;
    serialized: any;

    constructor(
        localValue: any,
        serialized: any,
        transform: (value: any) => any = (value: any) => value,
    ) {
        this.localValue = transform(localValue);
        this.serialized = transform(serialized);
    }

    public static wrap(value: Serializable | any, transform: (value: any) => any = (value: any) => value) {
        if(value instanceof Serializable) {
            return new Serializable(value.localValue, value.serialized, transform);
        }

        return new Serializable(value, value, transform);
    }
}

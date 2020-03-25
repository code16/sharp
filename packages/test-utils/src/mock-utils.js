export function setter(object, property) {
    return Object.getOwnPropertyDescriptor(object,property).set;
}

export function getter(object, property) {
    return Object.getOwnPropertyDescriptor(object,property).get;
}

// return mocked descriptor
export function mockProperty(object, property, defaultValue) {
    let value = defaultValue;

    let get = jest.fn(()=>value), set = jest.fn(v=>value=v);

    if(object.hasOwnProperty(property)) {
        let baseDescriptor = Object.getOwnPropertyDescriptor(object,property);
        get.unmock = () => Object.defineProperty(object, property, baseDescriptor);
    }
    else {
        get.unmock = () => delete object[property];
    }

    let descriptor = {
        get,set,
        configurable: true
    };

    Object.defineProperty(object,property,descriptor);

    return descriptor;
}

// return object
export function unmockProperty(object, property) {
    let { get } = Object.getOwnPropertyDescriptor(object,property) || {};

    if(!get || !get.unmock) {
        return;
    }
    get.unmock();
}
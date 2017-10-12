function mockComponent(component, customOptions={}) {
    let { template, render, ...extend } = customOptions;
    let name = component.name || customOptions.name;
    return Object.assign(component, {
        mixins: [],
        'extends': extend,
        template: template,
        render: !template ? render || function() { return this._v(`__MOCKED_SFC_${name}__\n`) } : null,
        watch: {},
        beforeCreate: ()=>{},
        created: ()=>{},
        beforeMount: ()=>{},
        mounted: ()=>{},
        beforeUpdate: ()=>{},
        updated: ()=>{},
        beforeDestroy: ()=>{},
        destroyed: ()=>{},
        data: ()=>({}),
        provide: {},
        inject: []
    });
}

export function mockChildrenComponents(toMock, { customs={} }={}) {
    Object.keys(toMock.components).forEach(compKey=>{
        mockComponent(toMock.components[compKey],customs[compKey]);
    });
    return toMock;
}

export function mockSFC (sfcModule, customOptions) {
    return mockComponent(sfcModule.default,customOptions);
}

export function unmockSFC(sfcModule) {
    let { 'default':defaultObj, ...options } = sfcModule;
    sfcModule.default = options;
}
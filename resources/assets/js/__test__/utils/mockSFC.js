export function mockSFC (sfcModule, { template, render, ...extend }={}, mockOptions={}) {

    let { isJsFile } = mockOptions;

    if(isJsFile) {
        sfcModule.__oldDefault__ = { ...sfcModule.default }
    }

    Object.assign(sfcModule.default, {
        mixins: [],
        'extends': extend,
        template: template,
        render: !template ? render || function() { return this._v(`__MOCKED_SFC_${sfcModule.name}__\n`) } : null,
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
        inject: [],
    });

    //console.log(sfcModule);
    return sfcModule.default;
}

export function unmockSFC(sfcModule) {
    if(sfcModule.__oldDefault__) {
        sfcModule.default = sfcModule.__oldDefault__;
        return;
    }

    let { 'default':defaultObj, ...options } = sfcModule;
    sfcModule.default = options;
}
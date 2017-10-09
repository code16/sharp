export function mockSFC (sfc, { render, template, ...extend } = {}) {
    Object.assign(sfc, {
        mixins: [],
        'extends': extend,
        template: template,
        render: !template ? render || function() { return this._v(`__MOCKED_SFC_${sfc.name}__\n`) } : null,
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
    })
}
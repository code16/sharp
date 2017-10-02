export function mockSFC (sfc, extend) {
    Object.assign(sfc, {
        mixins: [],
        'extends': extend,
        template: null,
        render() { return this._v(`__MOCKED_SFC_${sfc.name}__\n`) },
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
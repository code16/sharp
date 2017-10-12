export default {
    'basicSingleContainerFullAuthorizations': {
        containers: {
            title: {
                key: 'title',
                label: 'Titre'
            }
        },
        layout:[{
            key: 'title',
            size: 6,
            sizeXS: 12
        }],
        data:{
            items : [
                {
                    id: 1,
                    title: 'Super title'
                }
            ]
        },
        config:{
            filters:[],
            instanceIdAttribute: 'id'
        },
        authorizations:{
            view: true,
            update: true
        }
    }
}
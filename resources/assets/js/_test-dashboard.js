export const layout = {
    tabbed:false,
    tabs: [
        {
            title:'tab1',
            columns: [
                {
                    widgets: [
                        [
                            {
                                key: 'SuperPanel'
                            },
                            {
                                key:'Panel2'
                            }
                        ]
                    ]
                }
            ]
        }
    ]
};

export const data = {

};

export const widgets = {
    SuperPanel: {
        type:'panel',
        template:`
            <pre>Lorem ipsum de d e dz dez dez dez dezdez d ez dez dez
dezdezd
ezd
ezd
ezdezdededededededede</pre>
        `
    },
    Panel2: {
        type:'panel',
        template:'aaa'
    }
};
export const layout = [
    {
        "title": "Tab1",
        "columns": [
            {
                "size": 5,
                "fields": [
                    [
                        {
                            "key": "A",
                            "size": 4,
                            "sizeXS": 6
                        },
                        {
                            "key": "B",
                            "size": 8,
                            "sizeXS": 6
                        }
                    ],
                    [
                        {
                            "fieldset": "dates",
                            "fields": [
                                [
                                    {
                                        "key": "C"
                                    },
                                    {
                                        "key": "D"
                                    }
                                ],
                                [
                                    {
                                        "key": "E"
                                    }
                                ]
                            ]
                        }
                    ]
                ]
            },
            {
                "size": 7,
                "fields": [
                    [
                        {
                            "key": "F",
                            "item": [
                                [
                                    {
                                        "key": "F1"
                                    }, {
                                    "key": "F2"
                                }
                                ], [
                                    {
                                        "key": "F3"
                                    }
                                ]
                            ]
                        }
                    ], [
                        {
                            "key": "G"
                        }, {
                            "key": "H"
                        }
                    ], [
                        {
                            "key" : "name"
                        }
                    ], [
                        {
                            "key" : "myimage"
                        }
                    ]
                ]
            }
        ]
    }
];

export const data = {
    "A":"Valeur texte"
};

export const fields = {
    'A':{
        type:'text',
        label: 'Mon Label'
    },
    'B':{
        type:'text',
        label: '\u00A0'
    },
    'C':{
        type:'text'
    },
    'D':{
        type:'text'
    },
    'E':{
        type:'text'
    },
    'F':{
        type:'text'
    },
    'G':{
        type:'text'
    },
    'H':{
        type:'text'
    },
    'name':{
        type:'autocomplete',
        mode:'local',
        placeholder: 'Selectionnez un nom',
        searchMinChars:3,
        localValues: [
            { id:'A', value: 'Antoine', surname: 'Guingand' },
            { id:'B', value: 'Robert', surname: 'Martin' },
            { id:'C', value: 'François', surname: 'Leforestier' },
            { id:'D', value: 'Fernand', surname: 'Coli' }
        ],
        listItemTemplate:`
                            <img src="https://i.ytimg.com/vi/wSTt04rOwa8/maxresdefault.jpg" width="50px">
                            <span class="value">{{ value }}</span>
                            <span class="surname">{{ surname }}</span>
                        `,
        resultItemTemplate:`
                            Résultat : {{ value }} {{ surname }}
                        `,
        templateProps: ['value', 'surname'],
        searchKeys: ['value', 'surname'],
        // disabled: true
        conditionalDisplay: '!advanced_search:red,blue,orange'
    },
    'myimage': {
        type: 'upload',
        maxFileSize: 6,
        fileFilter: ['.jpg','.png'],
        thumbnail:'150x150'
    }
};

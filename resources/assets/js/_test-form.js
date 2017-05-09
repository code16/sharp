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
    "A":"Valeur texte",B:'',C:'',D:'',E:'',F:'',G:'',H:'',
    "name":"B",
    "myimage": {
        name:"doggo.jpg",
        size:14550,
        thumbnail:"https://i.ytimg.com/vi/wSTt04rOwa8/maxresdefault.jpg"
    }
};

export const fields = {
    'A':{
        type:'text',
        label: 'Texte'
    },
    'B':{
        type:'password',
        label: 'Mot de passe'
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
            { id:'A', name: 'Antoine', surname: 'Guingand' },
            { id:'B', name: 'Robert', surname: 'Martin' },
            { id:'C', name: 'François', surname: 'Leforestier' },
            { id:'D', name: 'Fernand', surname: 'Coli' }
        ],
        listItemTemplate:`
                            <img src="https://i.ytimg.com/vi/wSTt04rOwa8/maxresdefault.jpg" width="50px">
                            <span class="name">{{ name }}</span>
                            <span class="surname">{{ surname }}</span>
                        `,
        resultItemTemplate:`
                            Résultat : {{ name }} {{ surname }}
                        `,
        inline:true,
        templateProps: ['name', 'surname'],
        searchKeys: ['name', 'surname'],
        itemIdAttribute:'id',
        // disabled: true
        conditionalDisplay: '!advanced_search:red,blue,orange'
    },
    'myimage': {
        type: 'upload',
        maxFileSize: 6,
        fileFilter: ['.jpg','.jpeg','.png'],
        thumbnail:'150x150'
    }
};

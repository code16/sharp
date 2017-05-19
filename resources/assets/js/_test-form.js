export const layout = [
    {
        "title": "Tab1",
        "columns": [
            {
                "size": 7,
                "fields": [
                    [
                        {
                            "key": "A",
                            "size": 4,
                            "sizeXS": 6
                        },
                        {
                            "key": "B",
                            "sizeXS": 6
                        }
                    ],
                    [
                        {
                            "fieldset": "dates",
                            "fields": [
                                [
                                    {
                                        "key": "date"
                                    }
                                ],
                                [
                                    {
                                        "key": "number"
                                    }
                                ]
                            ]
                        }
                    ],
                    [
                        {
                            "key":"mdeditor",
                        }
                    ]
                ]
            },
            {
                "size": 5,
                "fields": [
                    [
                        {
                            "key": "mylist",
                            "item": [
                                [
                                    {
                                        "key": "name"
                                    }, {
                                    "key": "surname"
                                }
                                ], [
                                    {
                                        "key": "age"
                                    }
                                ]
                            ]
                        }
                    ], [
                        {
                            "key": "show_autocomplete"
                        }, {
                            "key": "show_upload"
                        }
                    ], [
                        {
                            "key" : "name"
                        }
                    ], [
                        {
                            "key": "admin_password"
                        }
                    ],[
                        {
                            "key" : "myimage"
                        }
                    ],
                    [
                        {
                            "key" : "select"
                        }
                    ]
                ]
            }
        ]
    }
];

export const data = {
    "A":"Valeur texte",
    "B":'',
    "number":1,
    "date":'2017-04-12 12:30:06',
    "show_autocomplete":true,
    "show_upload":true,
    "name":"B",
    "admin_password":"",
    "mdeditor":"",
    "myimage": {
        name:"doggo.jpg",
        size:14550,
        thumbnail:"https://i.ytimg.com/vi/wSTt04rOwa8/maxresdefault.jpg"
    },
    "mylist": [{
        name:'', surname:'', age:''
    }],
    //"select": [1,3]
    "select":1
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
    'number':{
        type:'number',
        showControls:false,
        min:1,
        max:10,
        step:2,
    },
    'show_autocomplete': {
        type:'check',
        text:'Afficher Autocomplete'
    },
    'show_upload': {
        type:'check',
        text:'Afficher Upload'
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
        conditionalDisplay: {
            key: 'show_autocomplete',
        },
    },
    'admin_password': {
        type:'password',
        conditionalDisplay: {
            not:1,
            key:'name',
            values:['C','D']
        }
    },
    'myimage': {
        type: 'upload',
        maxFileSize: 6,
        fileFilter: ['.jpg','.jpeg','.png'],
        thumbnail:'150x150',
        conditionalDisplay: {
            key: 'show_upload',
        },
    },
    'mylist': {
        type:'list',
        label:'Super liste',
        sortable: true,
        maxItemCount:5,
        collapsedItemTemplate:"{{ name && surname ? `${name} ${surname}` : `Nouvelle personne n°${$index}` }}",
        templateProps: ['name','surname','age'],
        itemFields: {
            'name': {
                label:'Nom',
                type:'text',
            },
            'surname': {
                label:'Prénom',
                type:'text'
            },
            'age': {
                label:'Âge',
                type:'text'
            }
        }
    },
    'date':{
        type:'date',
        hasTime:true,
        minDate:'2017-03-15'
    },
    'mdeditor': {
        type: 'markdown',
        height:250,
        placeholder:'super editeur',
        //toolbar: ["bold", "italic", "heading", "|", "quote"]
    },
    'select': {
        type:'select',
        //multiple:true,
        display:'dropdown',
        options: [
            {id:0,label:'Jérôme'}, {id:1,label:'François'}, {id:2,label:'Raymond'}, {id:3,label:'Claude'}, {id:4,label:'Antoine'}, {id:5,label:'Félicité'}
        ]
    }
};

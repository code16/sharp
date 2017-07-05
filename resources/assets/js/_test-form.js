//
const localized = true;
const readOnly = false;
//

const layout = {
    "tabbed":true,
    "tabs":
    [
        {
            "title": "Tab1",
            "columns": [
                {
                    "size": 6,
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
                                "legend": "dates",
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
                        ],
                        [{key:'tags'}]
                    ]
                },
                {
                    "size": 6,
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
                                            "key": "profile"
                                        }
                                    ]
                                ]
                            }
                        ],
                        [{"key": "show_autocomplete"}],
                        [{"key" : "name"}],
                        [{"key": "admin_password"}],
                        [{"key": "show_upload_1"},{"key": "show_upload_2"}],
                        [{"key" : "myimage"}],
                        [{"key" : "select"}],
                        [{"key":"show_html"}],
                        [{"key":"html"}]
                    ]
                }
            ]
        },
        {
            title:'tab2',
            columns:[
                {
                    "size":12,
                    "fields": [
                        [{"key":"test_tab_2"}]
                    ]
                }
            ]
        }
    ]
};

const localizedData = {
    A: {
        fr:'Un texte en français',
        en:'English text',
        de:'Deutsch arbeit'
    },
    mdeditor: {
        fr:"Français",
        en:"Anglais",
        de:"Allemand"
    },
    mylist: {
        fr:null,
        en:null,
        de:null
    }
};

const defaultData = {
    A:"Valeur texte",
    B:'',
    number:1,
    date:null,
    show_autocomplete:true,
    name:null,
    admin_password:"",
    mdeditor:"Salut",
    show_upload_1:true,
    show_upload_2:false,
    myimage: null,
    // myimage: {
    //     name:"doggo.jpg",
    //     size:14550,
    //     thumbnail:"img/chien.jpg"
    // },
    mylist: [],
    tags:[],
    select: [1,3],
    //select:1,
    show_html: true,
    html: {
        title: 'Salut',
        paragraphe: "Le select au dessus doit avoir François et Claude pour que je m'affiche"
    },
    test_tab_2: 'aaa'
};

const errors = {
    test_tab_2: ['Erreur de test'],
   // 'mylist.fr.0.name': ['Nom trop nul']
};

let data = defaultData;
let fields = {
    A:{
        type:'text',
        label: 'Texte'
    },
    B:{
        type:'password',
        label: 'Mot de passe'
    },
    number:{
        type:'number',
        showControls:false,
        min:1,
        max:10,
        step:2,
    },
    show_autocomplete: {
        type:'check',
        text:'Afficher Autocomplete'
    },
    show_upload: {
        type:'check',
        text:'Afficher Upload'
    },
    name:{
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
        searchKeys: ['name', 'surname'],
        itemIdAttribute:'id',
        conditionalDisplay: {
            operator:'or',
            fields: [{
                key:'show_autocomplete',
                values: true
            }]
        },
    },
    admin_password: {
        type:'password',
        label:'Mot de passe pour les administrateur',
        conditionalDisplay: {
            operator: 'or',
            fields: [{
                key:'name',
                values:['C','D']
            }]
        }
    },
    show_upload_1: {
        type: 'check',
        text: "Appuyer pour afficher l'upload"
    },
    show_upload_2: {
        type: 'check',
        text: "Ou ici"
    },
    myimage: {
        type: 'upload',
        maxFileSize: 300,
        fileFilter: ['.jpg','.jpeg','.png'],
        ratioX: 16,
        ratioY: 9,
        conditionalDisplay: {
            operator:'or',
            fields: [{
                key:'show_upload_1',
                values: true
            },{
                key:'show_upload_2',
                values: true
            }]
        },
    },
    mylist: {
        type:'list',
        label:'Super liste',
        sortable: true,
        addable: true,
        removable: true,
        itemIdAttribute: 'id',
        //maxItemCount:5,
        //collapsedItemTemplate:"{{ name && surname ? `${name} ${surname}` : `Nouvelle personne n°${$index}` }}",
        templateProps: ['name','surname','age'],
        itemFields: {
            'name': {
                label:'Nom',
                type:'text'
            },
            'surname': {
                label:'Prénom',
                type:'text'
            },
            'profile': {
                label:'Image de profile',
                type:'upload',
                maxFileSize: 300,
                fileFilter: ['.jpg','.jpeg','.png'],
            }
        },
    },
    date:{
        type:'date',
        label:'date',
        hasTime:true,
        stepTime:20,
        minTime:'8:20'
    },
    mdeditor: {
        type: 'markdown',
        height:250,
        placeholder:'super editeur',
        //readOnly: true
        //toolbar: ["bold", "italic", "heading", "|", "quote"]
    },
    tags: {
        type:'tags',
        creatable: true,
        createText: 'Ajouter ce tag',
        maxTagCount: 5,
        options: [
            {id:0,label:'Jérôme'}, {id:1,label:'François'}, {id:2,label:'Raymond'}, {id:3,label:'Claude'}, {id:4,label:'Antoine'}, {id:5,label:'Félicité'}
        ]
    },
    select: {
        type:'select',
        multiple:true,
        display:'dropdown',
        options: [
            {id:0,label:'Jérôme'}, {id:1,label:'François'}, {id:2,label:'Raymond'}, {id:3,label:'Claude'}, {id:4,label:'Antoine'}, {id:5,label:'Félicité'}
        ]
    },
    show_html: {
        type:'check',
        text:'Show HTML field'
    },
    html: {
        type:'html',
        template:'<h4>{{title}}</h4><p>{{paragraphe}}</p>',
        conditionalDisplay: {
            operator: 'and',
            fields: [{
                key: 'select',
                values: [1,3]
            },{
                key:'show_html',
                values: true
            }]
        }
    },
    test_tab_2: {
        type:'text',
        label:'Input de test ;)'
    }
};
let config = {};

let authorizations = {
    view:true, delete:true, create: true, update: true
};

if(localized) {
    config.locales = ['fr','en','de'];
    Object.keys(fields).forEach(k=>k in localizedData && (fields[k].localized = true));
    data = Object.assign(defaultData, localizedData);
}

if(readOnly) {
    Object.keys(fields).forEach(k=>fields[k].readOnly=true);
}

export {
    fields,
    data,
    layout,
    config,
    authorizations,
    errors
};

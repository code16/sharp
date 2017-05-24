/*

fieldKey : {
    type:String
}

fieldValue : {
    type:[String, Number, Boolean, Object, Array]
}

itemIdAttribute : {
    type:String,
    default: 'id'
}


{
    text: String,
    number: Number,
    textarea: String,
    markdown: String, //markdown format
    check: Boolean,
    autocomplete: String, // value of [itemIdAttribute]
    upload: {
        filename: String,
        thumbnail: String //optional : path to thumbnail
    },
    date: String, // date format
    period: {
        start: String, // date format
        end: String //date format
    },
        dropdown: String, // current value
    taginput: [
        String, //...
    ],
    list: [
        {
            [itemIdAttribute]:String,
            // list fields
            [fieldKey]:fieldValue, //...
        }
    ]
}
 */
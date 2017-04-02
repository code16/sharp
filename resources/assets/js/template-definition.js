export default {
    listItemTemplate: {
        wrapIn: 'li',
        propagateEvents: ['click'],
        props: {
            item: { // contains all passed props
                type: Object,
                required : true
            }
        }
    }
}
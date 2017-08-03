export default {
    methods: {
        updateScroll() {
            let { autoScrollOptions:options } = this;

            if(typeof options === 'function')
                options = options.apply(this, arguments);

            if(typeof options !== 'object')
                return;

            let { list:getList, item:getItem } = options;
            let item = typeof getItem === 'function' ? getItem() : getItem,
                list = typeof getList === 'function' ? getList() : getList;


            if(list && item) {
                list.scrollTop = item.offsetTop - list.offsetHeight/2. + item.offsetHeight/2.;
            }
        }
    }
}
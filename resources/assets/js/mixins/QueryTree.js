export default {
    methods: {
        findAscendant(compName) {
            let parent=this.$parent;
            while(parent && parent.$options.name !== compName)
                parent = parent.$parent;
            return parent;
        }
    }
}
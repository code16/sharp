export default {
    install(Vue) {
        Vue.prototype.$findChild = function(childName) {
            return this.$children.find(({$options:{name}}) => name === childName);
        };

        Vue.prototype.$findDeepChildren = function(childName) {
            let children = [];
            for(let child of this.$children) {
                if(child.$options.name === childName)
                    children.push(child);

                let foundChildren = child.$findDeepChildren(childName);
                if(foundChildren.length)
                    children = [ ...children, ...foundChildren ];
            }
            return children;
        }
    }
}
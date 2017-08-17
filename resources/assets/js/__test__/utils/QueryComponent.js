export default {
    install(Vue) {
        Vue.prototype.$findChild = function(childName) {
            return this.$children.find(({$options:{name}}) => name === childName);
        }
    }
}
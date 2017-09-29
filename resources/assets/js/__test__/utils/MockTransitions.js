export default {
    install(Vue) {
        window.getComputedStyle = jest.fn(() => ({
            animationDelay:'',
            animationDuration:'',
            transitionDelay:'',
            transitionDuration:''
        }));

        Vue.options.components.TransitionGroup.updated = () => {};
    }
}
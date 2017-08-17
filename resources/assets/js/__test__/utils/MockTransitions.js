export default {
    install() {
        window.getComputedStyle = jest.fn(() => ({
            animationDelay:'',
            animationDuration:'',
            transitionDelay:'',
            transitionDuration:''
        }));
    }
}
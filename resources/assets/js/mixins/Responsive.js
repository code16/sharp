export default function (breakpoint='sm') {
    return {
        data() {
            return {
                isViewportSmall: false
            }
        },
        mounted() {
            let id = `viewport-down-${breakpoint}`;
            let testElm = document.getElementById(id);

            if(!testElm) {
                testElm = document.createElement('div');
                testElm.id = id;
                testElm.classList.add(`d-${breakpoint}-none`);
                document.body.appendChild(testElm);
            }

            const update = () => {
                let { offsetWidth } = testElm;
                this.isViewportSmall = offsetWidth;
            };

            update();
            window.addEventListener('resize', update);
        }
    }
}
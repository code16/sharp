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
                testElm.classList.add(`hidden-${breakpoint}-down`);
                document.body.appendChild(testElm);
            }

            const update = () => {
                let { offsetWidth, offsetHeight } = testElm;
                this.isViewportSmall = !offsetWidth && !offsetHeight;
            };

            update();
            window.addEventListener('resize', update);
        }
    }
}